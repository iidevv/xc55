<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\Order\Transformer\OrderItem;

use XLite\API\Endpoint\Order\DTO\OrderItem\OrderItemOutput as OutputDTO;
use XC\ProductVariants\API\Endpoint\Order\DTO\OrderItem\OrderItemOutput as CurrentOutputDTO;
use XC\ProductVariants\Model\OrderItem as CurrentModel;
use XLite\API\Endpoint\Order\Transformer\OrderItem\OutputTransformer as ParentOutputTransformer;
use Exception;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OutputTransformer extends ParentOutputTransformer
{
    /**
     * @param CurrentModel $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        /** @var CurrentOutputDTO $dto */
        $dto = parent::transform($object, $to, $context);

        $dto->variant = $object->getVariant() ? $object->getVariant()->getId() : null;

        return $dto;
    }
}
