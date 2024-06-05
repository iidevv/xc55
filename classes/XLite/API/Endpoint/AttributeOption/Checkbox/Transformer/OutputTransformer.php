<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeOption\Checkbox\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\AttributeOption\Checkbox\DTO\AttributeOptionCheckboxOutput as OutputDTO;
use XLite\Model\Attribute;
use XLite\Model\AttributeOption as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Model $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->position = $object->getPosition();
        $dto->value = $object->getName();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class
            && $data instanceof Model
            && $data->getAttribute()->getType() === Attribute::TYPE_CHECKBOX;
    }
}
