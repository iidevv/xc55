<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Order page controller
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->params[] = 'message_id';
    }


    /**
     * Mark Read
     */
    protected function doActionMarkAsRead()
    {
        $message = $this->getReadUnreadMessage();

        if ($message) {
            $message->markAsRead();
            \XLite\Core\Event::orderMessagesCreate();
        }
    }

    /**
     * Mark Read
     */
    protected function doActionMarkAsUnread()
    {
        $message = $this->getReadUnreadMessage();

        if ($message) {
            $message->markAsUnread();
            \XLite\Core\Event::orderMessagesCreate();
        }
    }

    protected function getReadUnreadMessage()
    {
        $message_id = Request::getInstance()->message_id;
        $return = null;

        if ($message_id) {
            $return = Database::getRepo('XC\VendorMessages\Model\Message')->find($message_id);
        }

        return $return;
    }

    protected function createNewMessage()
    {
        $message = parent::createNewMessage();
        $files = Request::getInstance()->message_image;
        $files = is_array($files) ? $files : [];
        $message->processFiles('images', $files);
        return $message;
    }
}