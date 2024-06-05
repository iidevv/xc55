<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Profile;
use XC\VendorMessages\Model\Conversation;
use XC\VendorMessages\Model\Message;

/**
 * StaticProvider
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class StaticProvider extends \XC\ThemeTweaker\Core\Notifications\StaticProvider
{
    protected static function getNotificationsStaticData()
    {
        return parent::getNotificationsStaticData() + [
            'modules/XC/VendorMessages/new_message_notification' => [
                'message' => static::getMessageData(),
                'recipient' => static::getRecipient(),
            ],
            'modules/XC/VendorMessages/notification' => [
                'message' => static::getMessageData(),
                'recipient' => static::getRecipient(),
            ],
        ];
    }

    /**
     * Get message object
     *
     * @return Message
     */
    protected static function getMessageData()
    {
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findDumpProfile();
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findDumpOrder()
            ?: new \XLite\Model\Order(
                [
                    'profile' => new \XLite\Model\Profile(),
                ]
            );

        $conversation = new Conversation();
        $conversation->setId(9999999999);
        $conversation->setOrder($order);

        $message = new Message();
        $message->setDate(time())
            ->setType(Message::MESSAGE_TYPE_REGULAR)
            ->setAuthor($profile)
            ->setBody('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!')
            ->setConversation($conversation);

        return $message;
    }

    /**
     * Get profile object (recipent)
     *
     * @return Profile
     */
    protected static function getRecipient()
    {
        return new Profile();
    }
}
