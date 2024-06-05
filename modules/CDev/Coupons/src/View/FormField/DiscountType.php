<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\FormField;

/**
 * Discount type selector
 */
class DiscountType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            '%' => static::t('Percent'),
            '$' => static::t('X off', ['currency' => \XLite::getInstance()->getCurrency()->getCurrencySymbol()]),
        ];
    }
}
