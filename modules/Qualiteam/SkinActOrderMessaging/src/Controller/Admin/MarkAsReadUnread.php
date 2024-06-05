<?php


/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Order page controller
 */
class MarkAsReadUnread extends \XLite\Controller\Admin\AAdmin
{

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isAJAX();
    }

    /**
     * Mark Read
     */
    protected function doActionMarkAsRead()
    {
        $message = $this->getReadUnreadMessage();

        if ($message) {
            $read = $message->markAsRead();
            if ($read) {
                \XLite\Core\Database::getEM()->persist($read);
                \XLite\Core\Database::getEM()->flush();
            }
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
        }
    }

    protected function getReadUnreadMessage() {

        $message_id = Request::getInstance()->message_id;

        $return = null;

        if ($message_id) {
            $return = Database::getRepo('XC\VendorMessages\Model\Message')->find($message_id);
        }

        return $return;
    }
}