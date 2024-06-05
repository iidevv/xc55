<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\FormField\Select;

/**
 * Google reCAPTCHA v2.0 theme selector.
 */
class Theme extends \XLite\View\FormField\Select\Regular
{
    public const THEME_LIGHT = 'light';
    public const THEME_DARK  = 'dark';

    /**
     * Get default option.
     *
     * @return string
     */
    public static function getDefault()
    {
        return self::THEME_LIGHT;
    }

    /**
     * Get the list of available options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            self::THEME_LIGHT => 'light',
            self::THEME_DARK  => 'dark',
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
