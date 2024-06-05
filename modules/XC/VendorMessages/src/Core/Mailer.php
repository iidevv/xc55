<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XC\VendorMessages\Core\Mail\NewMessageNotification;
use XC\VendorMessages\Core\Mail\OrderMessageNotification;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    public const RECIPIENT_ADMIN    = 'A';
    public const RECIPIENT_CUSTOMER = 'C';

    /**
     * @param \XC\VendorMessages\Model\Message $message Message
     */
    public static function sendMessageNotifications(\XC\VendorMessages\Model\Message $message)
    {
        foreach ($message->getConversation()->getMembers() as $member) {
            if ($message->getAuthor()->getProfileId() !== $member->getProfileId()) {
                static::getBus()->dispatch(new SendMail(NewMessageNotification::class, [$message, $member]));
            }
        }

        if ($message->isShouldSendToAdmin()) {
            static::getBus()->dispatch(new SendMail(NewMessageNotification::class, [$message]));
        }
    }

    /**
     * @param \XC\VendorMessages\Model\Message $message Message
     */
    public static function sendOrderMessageNotifications(\XC\VendorMessages\Model\Message $message)
    {
        foreach ($message->getConversation()->getMembers() as $member) {
            if ($message->getAuthor()->getProfileId() !== $member->getProfileId()) {
                static::getBus()->dispatch(new SendMail(OrderMessageNotification::class, [$message, $member]));
            }
        }

        if ($message->isShouldSendToAdmin()) {
            static::getBus()->dispatch(new SendMail(OrderMessageNotification::class, [$message]));
        }
    }
}
