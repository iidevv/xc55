<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ContactUs\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use CDev\ContactUs\Core\Mail\ContactUsMessage;
use CDev\ContactUs\Model\Contact;

/**
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * @param Contact      $contact
     * @param string|array $email Email
     */
    public static function sendContactUsMessage(Contact $contact, $email)
    {
        static::getBus()->dispatch(new SendMail(ContactUsMessage::class, [$contact, $email]));
    }
}
