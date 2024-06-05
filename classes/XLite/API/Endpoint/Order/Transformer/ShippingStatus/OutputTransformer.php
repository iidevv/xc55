<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\ShippingStatus;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Order\DTO\ShippingStatus\OrderShippingStatusOutput as OutputDTO;
use XLite\Model\Order\Status\Shipping;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Shipping $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->code = $object->getCode();
        $dto->name = $object->getName();
        $dto->customer_name = $object->getCustomerName();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Shipping;
    }
}
