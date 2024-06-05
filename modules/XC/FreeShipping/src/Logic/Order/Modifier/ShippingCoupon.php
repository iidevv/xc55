<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate shipping modifier
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 * @Extender\After("XC\FreeShipping")
 */
class ShippingCoupon extends \XLite\Logic\Order\Modifier\Shipping
{
    /**
     * Return true if order item must be excluded from shipping rates calculations
     *
     * @return boolean
     */
    protected function isIgnoreShippingCalculation($item)
    {
        return parent::isIgnoreShippingCalculation($item)
            || $this->isAppliedFreeShippingCoupon($item);
    }

    /**
     * Return true if free shipping coupon is applied to specified order item
     *
     * @param \XLite\Model\OrderItem $item Order item model
     *
     * @return boolean
     */
    protected function isAppliedFreeShippingCoupon($item)
    {
        $result = false;

        if ($this->order->getUsedCoupons()) {
            foreach ($this->order->getUsedCoupons() as $coupon) {
                if ($coupon->getCoupon() && $coupon->getCoupon()->isFreeShipping()) {
                    $result = $coupon->getCoupon()->isValidForProduct($item->getProduct());
                }

                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }
}
