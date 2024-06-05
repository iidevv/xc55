<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Controller\GoogleRecaptchaTrait;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * Login page controller
 * @Extender\Mixin
 */
class Login extends \XLite\Controller\Customer\Login
{
    use GoogleRecaptchaTrait {
        showGoogleRecaptchaError as showGoogleRecaptchaTopError;
    }

    /**
     * Checks if Google reCAPTCHA validation is required.
     *
     * @return bool
     */
    protected function isGoogleRecaptchaRequired()
    {
        return Validator::getInstance()->isRequiredForLoginForm();
    }

    /**
     * Login action
     *
     * @return void
     */
    protected function doActionLogin()
    {
        if (!$this->isGoogleRecaptchaRequired() || $this->verifyGoogleRecaptcha()) {
            parent::doActionLogin();
        } else {
            $this->handleGoogleRecaptchaFailed();
        }
    }

    /**
     * Displays the Google reCAPTCHA error message on the page.
     *
     * @param string $error Error message
     *
     * @return void
     */
    protected function showGoogleRecaptchaError($error)
    {
        $this->showGoogleRecaptchaTopError($error);
        \XLite\Core\Event::invalidForm('login-form', static::t($error));
    }

    /**
     * Handles the Goolge reCAPTCHA error.
     *
     * @return void
     */
    protected function handleGoogleRecaptchaFailed()
    {
        $this->set('valid', false);
        $this->setReturnURL($this->buildFullURL('login'));
    }
}
