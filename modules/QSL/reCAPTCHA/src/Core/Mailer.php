<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XLite\Model\Profile;
use QSL\reCAPTCHA\Core\Mail\Profile\RecaptchaActivation;

/**
 * Mailer core class
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    /**
     * @param Profile $profile User profile
     */
    public static function sendRecaptchaActivationEmail(Profile $profile)
    {
        static::getBus()->dispatch(new SendMail(RecaptchaActivation::class, [$profile]));
    }
}
