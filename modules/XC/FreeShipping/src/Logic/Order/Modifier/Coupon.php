<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate discount coupon modifier
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\Logic\Order\Modifier\Discount
{
    /**
     * Return true if discount total is valid
     *
     * @param float $total Total
     *
     * @return boolean
     */
    protected function isValidTotal($total)
    {
        return $total >= 0
            && count($this->getUsedCoupons()) > 0;
    }
}
