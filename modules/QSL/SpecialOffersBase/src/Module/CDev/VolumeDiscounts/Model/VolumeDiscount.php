<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Module\CDev\VolumeDiscounts\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Order;

/**
 * Decorated coupon model
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\VolumeDiscounts")
 */
class VolumeDiscount extends \CDev\VolumeDiscounts\Model\VolumeDiscount
{
    /**
     * Get discount amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(Order $order)
    {
        $base = $this->getSpecialOffersDiscountBase($order);

        $discount = $this->isAbsolute()
            ? $this->getValue()
            : ($base * $this->getValue() / 100);

        return min($discount, $base);
    }

    /**
     * Get the base sum from which the discount is calculated.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    protected function getSpecialOffersDiscountBase(Order $order)
    {
        return $order->getSpecialOffersSubtotal();
    }
}
