<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Hidden\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\AttributeOption\Hidden\DTO\AttributeOptionHiddenOutput as OptionOutputDTO;
use XLite\API\Endpoint\AttributeOption\Hidden\Transformer\OutputTransformerInterface as OptionOutputTransformerInterfaceAlias;
use XLite\API\Endpoint\AttributeValue\Hidden\DTO\AttributeValueHiddenOutput as OutputDTO;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueHidden as Model;

final class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected OptionOutputTransformerInterfaceAlias $optionOutputTransformer;

    public function __construct(
        OptionOutputTransformerInterfaceAlias $optionOutputTransformer
    ) {
        $this->optionOutputTransformer = $optionOutputTransformer;
    }

    /**
     * @param Model $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();

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
            && $data->getAttribute()->getType() === Attribute::TYPE_HIDDEN;
    }
}
