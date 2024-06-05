<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\XC\NewsletterSubscriptions\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Controller\GoogleRecaptchaTrait;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * NewsletterSubscriptions controller
 * @Extender\Mixin
 */
class NewsletterSubscriptions extends \XC\NewsletterSubscriptions\Controller\Customer\NewsletterSubscriptions
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
        return Validator::getInstance()->isRequiredForNewsletterSubscriptions();
    }

    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        if (!$this->isGoogleRecaptchaRequired() || $this->verifyGoogleRecaptcha()) {
            parent::doActionSubscribe();
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
    }
}
