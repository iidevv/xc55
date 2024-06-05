<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Checkbox\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\AttributeValue\Checkbox\DTO\AttributeValueCheckboxOutput as OutputDTO;
use XLite\API\Endpoint\AttributeValue\Checkbox\Mapper\OutputPriceModifierTypeMapperInterface;
use XLite\API\Endpoint\AttributeValue\Checkbox\Mapper\OutputWeightModifierTypeMapperInterface;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueCheckbox as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected OutputPriceModifierTypeMapperInterface $outputPriceModifierTypeMapper;

    protected OutputWeightModifierTypeMapperInterface $outputWeightModifierTypeMapper;

    public function __construct(
        OutputPriceModifierTypeMapperInterface $outputPriceModifierTypeMapper,
        OutputWeightModifierTypeMapperInterface $outputWeightModifierTypeMapper
    ) {
        $this->outputPriceModifierTypeMapper = $outputPriceModifierTypeMapper;
        $this->outputWeightModifierTypeMapper = $outputWeightModifierTypeMapper;
    }

    /**
     * @param Model $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->value = $object->getValue();
        $dto->price_modifier = $object->getPriceModifier();
        $dto->price_modifier_type = $this->outputPriceModifierTypeMapper->map($object->getPriceModifierType());
        $dto->weight_modifier = $object->getWeightModifier();
        $dto->weight_modifier_type = $this->outputWeightModifierTypeMapper->map($object->getWeightModifierType());
        $dto->is_default = $object->getDefaultValue();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class
            && $data instanceof Model
            && $data->getAttribute()->getType() === Attribute::TYPE_CHECKBOX;
    }
}
