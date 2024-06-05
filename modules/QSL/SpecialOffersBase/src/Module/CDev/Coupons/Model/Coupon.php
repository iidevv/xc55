<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Module\CDev\Coupons\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated coupon model
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\Model\Coupon
{
    /**
     * Get order total
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    protected function getOrderTotal(\XLite\Model\Order $order)
    {
        return parent::getOrderTotal($order) + array_reduce($this->getValidOrderItems($order), static function ($carry, $item) {
            return $carry + $item->getSurchargeSum();
        }, 0);
    }
}
