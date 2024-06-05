<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\FormField\Select;

/**
 * Google reCAPTCHA v2.0 widget size selector.
 */
class Size extends \XLite\View\FormField\Select\Regular
{
    public const SIZE_NORMAL  = 'normal';
    public const SIZE_COMPACT = 'compact';

    /**
     * Get default option.
     *
     * @return string
     */
    public static function getDefault()
    {
        return self::SIZE_NORMAL;
    }

    /**
     * Get the list of available options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            self::SIZE_NORMAL  => 'normal',
            self::SIZE_COMPACT => 'compact',
        ];
    }

    /**
     * Get default option.
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return static::getDefault();
    }
}
