<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\API\Endpoint\ProductUpsellingProduct\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO\ProductUpsellingProductOutput as OutputDTO;
use XC\Upselling\Model\UpsellingProduct as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Model $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->product_id = $object->getProduct()->getProductId();
        $dto->position = $object->getPosition();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
