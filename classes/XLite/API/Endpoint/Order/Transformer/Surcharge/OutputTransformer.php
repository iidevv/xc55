<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\Surcharge;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Order\DTO\Surcharge\OrderSurchargeOutput as OutputDTO;
use XLite\Model\Order\Surcharge;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Surcharge $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->code = $object->getCode();
        $dto->value = $object->getValue();
        $dto->class = $object->getClass();
        $dto->name = $object->getName();
        $dto->weight = $object->getWeight();
        $dto->available = $object->getAvailable();
        $dto->include = $object->getInclude();
        $dto->type = $object->getType();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Surcharge;
    }
}
