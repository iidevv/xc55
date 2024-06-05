<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\ExternalSDK;

use XCart\Extender\Mapping\ListChild;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * reCAPTCHA SDK loader
 *
 * @ListChild (list="body", zone="customer", weight="999997")
 */
class ReCAPTCHA extends \XLite\View\ExternalSDK\AExternalSDK
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return false;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/reCAPTCHA/sdk/recaptcha.twig';
    }

    /**
     * Generate Google reCAPTCHA API URL (for APIv3 mode)
     *
     * @return string
     */
    protected function getSDKUrl()
    {
        $key  = $this->getPublicKey();
        $lang = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        return "https://www.google.com/recaptcha/api.js?render={$key}&hl={$lang}&onload=initRecaptchaV3";
    }

    /**
     * Returns the public site key for Google reCAPTCHA.
     *
     * @return string
     */
    protected function getPublicKey()
    {
        return \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_public ?: '';
    }

    /**
     * Get "action" param for recaptcha v3 API
     *
     * @see https://developers.google.com/recaptcha/docs/v3#actions
     *
     * @return string
     */
    protected function getActionAttribute()
    {
        return str_replace('_', '', \XLite::getController()->getTarget());
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Validator::getInstance()->isAPIv3SDKRequired();
    }
}
