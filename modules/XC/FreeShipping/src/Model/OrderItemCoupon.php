<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate OrderItem model
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 * @Extender\After("XC\FreeShipping")
 */
class OrderItemCoupon extends \XLite\Model\OrderItem
{
    /**
     * Return true if order item is forced to be 'free shipping' item
     *
     * @return boolean
     */
    public function isFreeShipping()
    {
        $result = parent::isFreeShipping();

        if (!$result && $this->getOrder()->getUsedCoupons()) {
            foreach ($this->getOrder()->getUsedCoupons() as $coupon) {
                if (
                    !$coupon->isDeleted()
                    && $coupon->getCoupon()->isFreeShipping()
                    && $coupon->getCoupon()->isValidForProduct($this->getProduct())
                ) {
                    // Product is affected by discount coupon 'FREE SHIPPING'
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }
}
