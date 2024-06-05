<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeGroup\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\AttributeGroup\DTO\AttributeGroupOutput as OutputDTO;
use XLite\Model\AttributeGroup;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param AttributeGroup $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->position = $object->getPosition();
        $dto->name = $object->getName();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof AttributeGroup;
    }
}
