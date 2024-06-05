<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Core\Mail;

use XLite\Core\Config;
use XLite\Model\Profile;
use XC\VendorMessages\Model\Message;

class NewMessageNotification extends AMessageNotification
{
    public static function getDir()
    {
        return 'modules/XC/VendorMessages/new_message_notification';
    }

    public function __construct(Message $message, Profile $recipient = null)
    {
        parent::__construct($message, $recipient);

        if ($recipient) {
            $this->appendData(['recipient' => $recipient]);
        } else {
            $this->appendData(['recipient_name' => Config::getInstance()->Company->company_name]);
        }
    }
}
