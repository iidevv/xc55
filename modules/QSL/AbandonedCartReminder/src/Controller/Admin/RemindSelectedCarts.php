<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

use XLite\Core\Auth;

/**
 * Controller for the Remind Selected Carts popup.
 */
class RemindSelectedCarts extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Whether there are defined reminder templates.
     *
     * @var boolean
     */
    protected $hasReminders;

    /**
     * Check ACL permissions.
     *
     * @return boolean
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
        return static::t('Choose reminder');
    }

    /**
     * Check whether there are defined reminder templates.
     *
     * @return boolean
     */
    public function hasReminders()
    {
        if (!isset($this->hasReminders)) {
            $count = \XLite\Core\Database::getRepo('QSL\AbandonedCartReminder\Model\Reminder')
                ->search(new \XLite\Core\CommonCell(), true);
            $this->hasReminders = 0 < $count;
        }

        return $this->hasReminders;
    }

    /**
     * Return URL to the "Add reminder templates" page.
     *
     * @return string
     */
    public function getAddRemindersLink()
    {
        return \XLite\Core\Converter::buildURL('cart_reminders');
    }
}
