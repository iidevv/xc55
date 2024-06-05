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
 * Checkout controller ("Continue as guest" form)
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    use GoogleRecaptchaTrait {
        showGoogleRecaptchaError as showGoogleRecaptchaTopError;
    }

    // protected $googleRecaptchaError = '';

    /**
     * Checks if Google reCAPTCHA validation is required.
     *
     * @return bool
     */
    protected function isGoogleRecaptchaRequired()
    {
        return Validator::getInstance()->isRequiredForRegistrationForm();
    }

    public function isGoogleRecaptchaRequiredForAnonymous()
    {
        return Validator::getInstance()->isRequiredForRegistrationForm();
    }

    /**
     * Recover Password action
     *
     * @return void
     */
    protected function doActionUpdateProfile()
    {
        if (!$this->isGoogleRecaptchaRequired() || $this->verifyGoogleRecaptcha()) {
            parent::doActionUpdateProfile();
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
        $this->setGoogleRecaptchaError(static::t($error));
        // \XLite\Core\Event::invalidForm('continue-as-guest-form', 'reCAPTCHA error:' . static::t($error));
    }

    public function setGoogleRecaptchaError($message)
    {
        \XLite\Core\Session::getInstance()->googleRecaptchaError = $message;
    }

    public function getGoogleRecaptchaError()
    {
        if (!isset($this->getGoogleRecaptchaErrorMessage)) {
            $this->getGoogleRecaptchaErrorMessage = '';

            if (!empty(\XLite\Core\Session::getInstance()->googleRecaptchaError)) {
                $this->getGoogleRecaptchaErrorMessage = \XLite\Core\Session::getInstance()->googleRecaptchaError;
                unset(\XLite\Core\Session::getInstance()->googleRecaptchaError);
            }
        }

        return $this->getGoogleRecaptchaErrorMessage;
    }

    /**
     * Handles the Goolge reCAPTCHA error.
     *
     * @return void
     */
    protected function handleGoogleRecaptchaFailed()
    {
        $this->set('valid', false);
        $this->setReturnURL($this->buildFullURL('checkout'));
    }
}
