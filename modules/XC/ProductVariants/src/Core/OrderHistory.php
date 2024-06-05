<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Core;

use XCart\Extender\Mapping\Extender;

/**
 * XPayments client
 *
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\Core\OrderHistory
{
    /**
     * Register the change amount inventory
     *
     * @param integer                                               $orderId Order identifier
     * @param \XC\ProductVariants\Model\ProductVariant $variant Product variant object
     * @param integer                                               $delta   Inventory delta changes
     *
     * @return void
     */
    public function registerChangeVariantAmount($orderId, $variant, $delta)
    {
        /** @var \XLite\Model\Product $product */
        $product = $variant->getProduct();

        if (!$variant->getDefaultAmount() || $product->getInventoryEnabled()) {
            $this->registerEvent(
                $orderId,
                static::CODE_CHANGE_AMOUNT,
                $this->getOrderChangeAmountDescription($orderId, $delta, $product),
                $this->getOrderChangeAmountData($orderId, $product->getName(), $variant->getPublicAmount() - $delta, $delta)
            );
        }
    }
}
