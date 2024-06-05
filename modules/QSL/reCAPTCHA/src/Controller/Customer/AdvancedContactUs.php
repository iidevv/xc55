<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Controller\GoogleRecaptchaTrait;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;
use QSL\reCAPTCHA\Main;

/**
 * Advanced contact us form controller
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\AdvancedContactUs")
 */
class AdvancedContactUs extends \QSL\AdvancedContactUs\Controller\Customer\AdvancedContactUs
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
        return Main::isACUIntegrated() && Validator::getInstance()->isRequiredForContactForm();
    }

    /**
     * Send action
     *
     * @return void
     */
    protected function doActionSend()
    {
        if (!$this->isGoogleRecaptchaRequired() || $this->verifyGoogleRecaptcha()) {
            parent::doActionSend();
        } else {
            $this->handleGoogleRecaptchaFailed();
        }
    }

    /**
     * Handles the Goolge reCAPTCHA error.
     *
     * @return void
     */
    protected function handleGoogleRecaptchaFailed()
    {
        $this->set('valid', false);
        $this->setReturnURL($this->buildFullURL('advanced_contact_us'));
    }
}
