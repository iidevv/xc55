<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

use QSL\AbandonedCartReminder\View\ItemsList\Model\Reminder as ItemsList;

/**
 * Controller for the Manage Cart Reminders page.
 */
class CartReminders extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions.
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Email notifications');
    }

    /**
     * Get search condition parameter by name.
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * Update list.
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new ItemsList();
        $list->processQuick();
    }

    /**
     * Save search conditions.
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $cellName = ItemsList::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            ItemsList::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions.
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = ItemsList::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }

    /**
     * Check whether the search box is visible, or not.
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('QSL\AbandonedCartReminder\Model\Reminder')->count();
    }
}
