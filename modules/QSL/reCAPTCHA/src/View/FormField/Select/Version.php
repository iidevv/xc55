<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\FormField\Select;

/**
 * Google reCAPTCHA API version selector.
 */
class Version extends \XLite\View\FormField\Select\Regular
{
    // https://developers.google.com/recaptcha/intro
    public const API_V2 = 'v2.0';
    public const API_V3 = 'v3.0';
    // https://developers.google.com/recaptcha/docs/invisible
    public const API_INVISIBLE = 'invisible';

    /**
     * Get the list of available options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            self::API_V2 => 'reCAPTCHA v2',
            self::API_V3 => 'reCAPTCHA v3 (Beta version)',
            // self::API_INVISIBLE  => 'reCAPTCHA Invisible',
        ];
    }

    /**
     * Get default option.
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return self::API_V2;
    }
}
