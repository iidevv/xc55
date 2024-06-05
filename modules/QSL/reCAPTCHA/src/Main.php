<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    public static function getRecaptchaApiVersion()
    {
        return \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_api;
    }

    public static function isAPIv2()
    {
        return static::getRecaptchaApiVersion() === \QSL\reCAPTCHA\View\FormField\Select\Version::API_V2;
    }

    public static function isAPIv3()
    {
        return static::getRecaptchaApiVersion() === \QSL\reCAPTCHA\View\FormField\Select\Version::API_V3;
    }

    /**
     * Returns False if QSL-AdvancedContactUs is using itw own captcha implementation
     *
     * @return boolean
     */
    public static function isACUIntegrated()
    {
        return method_exists(\QSL\AdvancedContactUs\Main::class, 'usingRecaptchaModule')
            && \QSL\AdvancedContactUs\Main::usingRecaptchaModule();
    }
}
