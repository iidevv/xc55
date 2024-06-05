<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\XC\ProductVariants\Logic\Action\Base;

use XCart\Extender\Mapping\Extender;
use XLite\Model\OrderItem;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
abstract class AProduct extends \CDev\GoogleAnalytics\Logic\Action\Base\AProduct
{
    public function __construct(Product $product, string $listName = '', int $position = 0, string $coupon = '', $qty = null, ?OrderItem $orderItem = null)
    {
        parent::__construct($product, $listName, $position, $coupon, $qty, $orderItem);
        if (
            $this->product instanceof Product
            && $orderItem instanceof OrderItem
            && $orderItem->getVariant()
        ) {
            // forcefully change default variant to use in GA requests
            $this->product->setRuntimeDefaultVariant($orderItem->getVariant());
        }
    }
}
