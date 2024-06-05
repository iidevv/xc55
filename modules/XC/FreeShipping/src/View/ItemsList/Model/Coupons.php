<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Coupons list
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class Coupons extends \CDev\Coupons\View\ItemsList\Coupons
{
    /**
     * Preprocess value for Discount column
     *
     * @param mixed                                   $value  Value
     * @param array                                   $column Column data
     * @param \CDev\Coupons\Model\Coupon $coupon Entity
     *
     * @return string
     */
    protected function preprocessValue($value, array $column, \CDev\Coupons\Model\Coupon $coupon)
    {
        return $coupon->isFreeShipping()
            ? static::t('Free shipping')
            : parent::preprocessValue($value, $column, $coupon);
    }
}
