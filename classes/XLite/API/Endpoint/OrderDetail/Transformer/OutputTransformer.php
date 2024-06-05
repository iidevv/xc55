<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderDetail\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use XLite\API\Endpoint\OrderDetail\DTO\OrderDetailOutput as OutputDTO;
use XLite\Model\OrderDetail;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param OrderDetail $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getDetailId();
        $dto->name = $object->getName();
        $dto->value = $object->getValue();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof OrderDetail;
    }
}
