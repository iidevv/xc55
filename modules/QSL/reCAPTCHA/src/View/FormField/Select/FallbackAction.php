<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\FormField\Select;

/**
 * Google reCAPTCHA API v3: fallback action selector
 */
class FallbackAction extends \XLite\View\FormField\Select\Regular
{
    public const ACTION_DO_NOTHING             = '';
    public const ACTION_DENY_FORM              = 'denyForm';
    public const ACTION_THROTTLE               = 'throttle';
    public const ACTION_SEND_CONFIRMATION_LINK = 'sendConfirmationLink';

    public const PARAM_RECAPTCHA_FALLBACK_FOR = 'recaptchaFallbackFor';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_RECAPTCHA_FALLBACK_FOR => new \XLite\Model\WidgetParam\TypeString('Fallback for'),
        ];
    }

    /**
     * Get the list of available options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $res = [
            self::ACTION_DO_NOTHING => static::t('Do nothing'),
            self::ACTION_DENY_FORM  => static::t('Deny using the form'),
        ];

        if (in_array($this->name, ['recaptcha_login_fallback', 'recaptcha_recover_fallback', 'recaptcha_contact_fallback', 'recaptcha_newsletter_fallback'])) {
            $res[self::ACTION_THROTTLE] = static::t('Throttle number of requests per period');
        }

        if (in_array($this->name, ['recaptcha_register_fallback'])) {
            $res[self::ACTION_SEND_CONFIRMATION_LINK] = static::t('Email confirmation link');
        }

        return $res;
    }

    /**
     * Get default option.
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return self::ACTION_DENY_FORM;
    }
}
