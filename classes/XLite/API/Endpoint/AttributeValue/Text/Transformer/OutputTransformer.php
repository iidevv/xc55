<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Text\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\AttributeValue\Text\DTO\AttributeValueTextOutput as OutputDTO;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueText as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Model $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->value = $object->getValue();
        $dto->editable = $object->getEditable();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class
            && $data instanceof Model
            && $data->getAttribute()->getType() === Attribute::TYPE_TEXT;
    }
}
