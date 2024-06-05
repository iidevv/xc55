<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ContactUs\Core\Mail;

use XLite\Core\Mailer;
use XLite\Core\Translation;

class ContactUsMessage extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/CDev/ContactUs/message';
    }

    protected static function defineVariables()
    {
        return [
                'message_author'  => Translation::lbl('Author'),
                'author_email'    => 'recipient@example.com',
                'message_subject' => Translation::lbl('Subject'),
                'message'         => Translation::lbl('Text'),
            ] + parent::defineVariables();
    }

    /**
     * ContactUsMessage constructor.
     *
     * @param \CDev\ContactUs\Model\Contact $contact
     * @param array|string                  $emails
     */
    public function __construct(\CDev\ContactUs\Model\Contact $contact, $emails)
    {
        parent::__construct();

        $this->setTo($emails);
        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->appendData(['contact' => $contact]);
        $this->populateVariables([
            'message_author'  => htmlspecialchars($contact->getName()),
            'author_email'    => htmlspecialchars($contact->getEmail()),
            'message_subject' => htmlspecialchars($contact->getSubject()),
            'message'         => htmlspecialchars($contact->getMessage()),
        ]);
        $this->addReplyTo([
            'address' => $contact->getEmail(),
            'name'    => $contact->getName(),
        ]);
    }
}
