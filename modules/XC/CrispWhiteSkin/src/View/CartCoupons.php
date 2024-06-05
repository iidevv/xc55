<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Cart coupons
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 * @Extender\After("XC\CrispWhiteSkin")
 */
abstract class CartCoupons extends \CDev\Coupons\View\CartCoupons
{
    /**
     * @return boolean
     */
    protected function isFieldOnly()
    {
        return false;
    }
}
