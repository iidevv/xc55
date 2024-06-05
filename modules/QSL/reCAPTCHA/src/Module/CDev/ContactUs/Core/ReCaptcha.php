<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\CDev\ContactUs\Core;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * Class ReCaptcha
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\ContactUs")
 */
class ReCaptcha extends \CDev\ContactUs\Core\ReCaptcha
{
    /**
     * Check if this configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        // \CDev\ContactUs\Controller\Customer\ContactUs class uses
        // this method to determine whether reCAPTCHA should be used, or not.
        // So, we do so too.
        return Validator::getInstance()->isRequiredForContactForm();
    }

    /**
     * Verify response
     *
     * @param $response
     *
     * @return mixed
     */
    public function verify($response)
    {
        // Although we use a different class as the reCAPTCHA response, it has
        // the same isSuccess() method, so we return it instead of the one that
        // is expected by ContactUs module.
        return Validator::getInstance()->verify($response);
    }
}
