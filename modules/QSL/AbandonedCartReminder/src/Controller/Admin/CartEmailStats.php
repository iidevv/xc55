<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Core\Converter;
use XLite\Core\TopMessage;
use XLite\Core\Database;
use DateTime;

/**
 * Controller for the Reminder Statistics page.
 */
class CartEmailStats extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions.
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL()
            || Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Abandoned cart statistics');
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return 'QSL\AbandonedCartReminder\View\ItemsList\Table\CartEmailStatistics';
    }

    /**
     * Controller action: delete statistics on abandoned cart e-mails.
     */
    public function doActionClearStats()
    {
        $date = trim(Request::getInstance()->date);
        $time = Converter::parseFromJsFormat($date);

        if ($time) {
            $midnight = (new DateTime('now', Converter::getTimeZone()))
                ->setTimestamp($time)
                ->modify('00:00:00')
                ->getTimestamp();

            Database::getRepo('QSL\AbandonedCartReminder\Model\Email')
                ->deletePastEmails($midnight);

            TopMessage::addInfo(
                static::t('Statistics on past abandoned cart e-mails have been cleared')
            );
        } else {
            TopMessage::addError(static::t('Wrong date format'));
        }
    }
}
