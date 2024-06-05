<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\Order\DTO\OrderItem;

use XLite\API\Endpoint\Order\DTO\OrderItem\OrderItemOutput as ParentOrderItemOutput;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderItemOutput extends ParentOrderItemOutput
{
    /**
     * @var int|null
     */
    public ?int $variant = null;
}
