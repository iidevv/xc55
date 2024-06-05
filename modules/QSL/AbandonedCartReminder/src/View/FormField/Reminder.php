<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormField;

use QSL\AbandonedCartReminder\Model\Repo\Reminder as Repo;

/**
 * Reminder selector.
 */
class Reminder extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return available reminders.
     *
     * @return array
     */
    protected function getReminderList()
    {
        $list = [];

        $reminders = \XLite\Core\Database::getRepo('QSL\AbandonedCartReminder\Model\Reminder')
            ->search($this->getReminderSearchConditions());

        foreach ($reminders as $reminder) {
            $list[$reminder->getReminderId()] = $reminder->getName();
        }

        return $list;
    }

    /**
     * Prepares the search condition for retrieving reminder templates.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getReminderSearchConditions()
    {
        return new \XLite\Core\CommonCell(
            [
                Repo::P_ORDER_BY => [
                    [
                        Repo::SORT_BY_MODE_ENABLED,
                        'DESC',
                    ],
                    [
                        Repo::SORT_BY_MODE_DELAY,
                        'ASC',
                    ]
                ],
            ]
        );
    }

    /**
     * Return default options for the selector.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getReminderList();
    }
}
