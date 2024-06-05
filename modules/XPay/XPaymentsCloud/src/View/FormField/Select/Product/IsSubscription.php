<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\FormField\Select\Product;

use XLite\View\FormField\Select\ASelect;

/**
 * Product status selector - whether it is a subscription or not
 */
class IsSubscription extends ASelect
{
    const YES = 'Y';
    const NO  = 'N';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''        => 'Any products',
            self::YES => 'Only products with subscription plan',
            self::NO  => 'Only products without subscription plan',
        ];
    }

}
