<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Select\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\AttributeOption\Select\DTO\AttributeOptionSelectOutput as OptionOutputDTO;
use XLite\API\Endpoint\AttributeOption\Select\Transformer\OutputTransformerInterface as OptionOutputTransformerInterfaceAlias;
use XLite\API\Endpoint\AttributeValue\Select\DTO\AttributeValueSelectOutput as OutputDTO;
use XLite\API\Endpoint\AttributeValue\Select\Mapper\OutputPriceModifierTypeMapperInterface;
use XLite\API\Endpoint\AttributeValue\Select\Mapper\OutputWeightModifierTypeMapperInterface;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueSelect as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected OptionOutputTransformerInterfaceAlias $optionOutputTransformer;

    protected OutputPriceModifierTypeMapperInterface $outputPriceModifierTypeMapper;

    protected OutputWeightModifierTypeMapperInterface $outputWeightModifierTypeMapper;

    public function __construct(
        OptionOutputTransformerInterfaceAlias $optionOutputTransformer,
        OutputPriceModifierTypeMapperInterface $outputPriceModifierTypeMapper,
        OutputWeightModifierTypeMapperInterface $outputWeightModifierTypeMapper
    ) {
        $this->optionOutputTransformer = $optionOutputTransformer;
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
        $dto->position = $object->getPosition();
        $dto->price_modifier = $object->getPriceModifier();
        $dto->price_modifier_type = $this->outputPriceModifierTypeMapper->map($object->getPriceModifierType());
        $dto->weight_modifier = $object->getWeightModifier();
        $dto->weight_modifier_type = $this->outputWeightModifierTypeMapper->map($object->getWeightModifierType());
        $dto->is_default = $object->getDefaultValue();

        $dto->option = $this->optionOutputTransformer->transform(
            $object->getAttributeOption(),
            OptionOutputDTO::class,
            $context
        );

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class
            && $data instanceof Model
            && $data->getAttribute()->getType() === Attribute::TYPE_SELECT;
    }
}
