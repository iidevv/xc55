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
 * Password recovery controller
 * @Extender\Mixin
 */
class RecoverPassword extends \XLite\Controller\Customer\RecoverPassword
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
        return Validator::getInstance()->isRequiredForRecoveryForm();
    }

    /**
     * Recover Password action
     *
     * @return void
     */
    protected function doActionRecoverPassword()
    {
        if (!$this->isGoogleRecaptchaRequired() || $this->verifyGoogleRecaptcha()) {
            parent::doActionRecoverPassword();
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
        if (!$this->isAJAX()) {
            $this->showGoogleRecaptchaTopError($error);
        }

        \XLite\Core\Event::invalidForm('recovery-form', $error);
    }

    /**
     * Handles the Goolge reCAPTCHA error.
     *
     * @return void
     */
    protected function handleGoogleRecaptchaFailed()
    {
        $this->set('valid', false);
        $this->setReturnURL($this->buildURL('recover_password'));
    }
}
