<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Controller\Admin;

class OrderReturns extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Orders');
    }

    protected function doActionUpdate()
    {
        $list = new \QSL\Returns\View\ItemsList\Model\OrderReturn();

        $list->processQuick();
    }

    protected function doActionDeleteOrderReturns()
    {
        $select = \XLite\Core\Request::getInstance()->delete;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('QSL\Returns\Model\OrderReturn')->deleteReturnsById($select);

            \XLite\Core\TopMessage::addInfo(
                'Information has been successfully deleted'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the records first');
        }
    }

    // {{{ Search

    /**
     * Get search condition parameter by name
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

    protected function doActionSearch()
    {
        $cellName = \QSL\Returns\View\ItemsList\Model\OrderReturn::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            \QSL\Returns\View\ItemsList\Model\OrderReturn::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \QSL\Returns\View\ItemsList\Model\OrderReturn::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }

    // }}}
}
