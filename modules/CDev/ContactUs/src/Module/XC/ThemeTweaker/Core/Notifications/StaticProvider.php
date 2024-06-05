<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ContactUs\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Mailer;
use CDev\ContactUs\Model\Contact;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class StaticProvider extends \XC\ThemeTweaker\Core\Notifications\StaticProvider
{
    protected static function getNotificationsStaticData()
    {
        return parent::getNotificationsStaticData() + [
                'modules/CDev/ContactUs/message' => [
                    'contact' => static::getContactMock(),
                    'emails'  => Mailer::getSiteAdministratorMails(),
                ],
            ];
    }

    /**
     * @return Contact
     */
    protected static function getContactMock()
    {
        $contact = new Contact();
        $contact->setSubject('Test message')
            ->setName('John Doe')
            ->setEmail('email@example.com')
            ->setMessage('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');

        return $contact;
    }
}
