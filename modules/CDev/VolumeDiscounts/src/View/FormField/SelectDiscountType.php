<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\View\FormField;

/**
 * Discount type (% or $) selector widget
 */
class SelectDiscountType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Available options
     */
    public const VALUE_PERCENT  = '%';
    public const VALUE_ABSOLUTE = '$';


    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            self::VALUE_PERCENT  => '%',
            self::VALUE_ABSOLUTE => \XLite::getInstance()->getCurrency()->getCurrencySymbol(),
        ];
    }
}
