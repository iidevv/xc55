<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Attribute\Checkbox\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Attribute\Checkbox\DTO\AttributeCheckboxOutput as OutputDTO;
use XLite\Model\Attribute as Model;

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
        $dto->name = $object->getName();
        $dto->groupId = $object->getAttributeGroup() ? $object->getAttributeGroup()->getId() : null;

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
