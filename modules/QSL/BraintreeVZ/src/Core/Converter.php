<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Miscellaneous conversion routines
 * @Extender\Mixin
 */
class Converter extends \XLite\Core\Converter
{
    /**
     * Round price for Braintree
     *
     * @param float $value
     *
     * @return float
     */
    public static function prepareBraintreePrice($value)
    {
        return round($value, 2);
    }

}
