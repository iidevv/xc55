<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Controller\Features\SearchByFilterTrait;

class QuickbooksSyncOrders extends QuickbooksSyncData
{
    use SearchByFilterTrait;
    
    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: 'Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncOrders';
    }
    
    // {{{ Search
    /**
     * Save search conditions
     */
    protected function doActionSearchItemsList()
    {
        // Clear stored filter within stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }
    
    /**
     * Clear search conditions
     */
    protected function doActionClearSearch()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }
    
    /**
     * Initialize search parameters from request data
     */
    protected function prepareSearchParams()
    {
        $ordersSearch = $this->getSearchFilterParams();

        if (!$ordersSearch) {
            // Prepare dates
            $this->startDate = $this->getDateValue('startDate');
            $this->endDate   = $this->getDateValue('endDate', true);

            if (
                $this->startDate === 0
                || $this->endDate === 0
                || $this->startDate > $this->endDate
            ) {
                $date = getdate(\XLite\Core\Converter::time());

                $this->startDate = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
                $this->endDate   = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
            }

            foreach ($this->getSearchParams() as $modelParam => $requestParam) {
                if ($requestParam === \XLite\Model\Repo\Order::P_DATE) {
                    $ordersSearch[$requestParam] = [$this->startDate, $this->endDate];
                } elseif (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                    $ordersSearch[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
                }
            }

            if (!isset($ordersSearch[\XLite\Model\Repo\Order::P_PROFILE_ID])) {
                $ordersSearch[\XLite\Model\Repo\Order::P_PROFILE_ID] = 0;
            }
        }

        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = $ordersSearch;
    }
    
    /**
     * getDateValue
     * FIXME - to remove
     *
     * @param string  $fieldName Field name (prefix)
     * @param bool $isEndDate End date flag OPTIONAL
     *
     * @return integer
     */
    public function getDateValue($fieldName, $isEndDate = false)
    {
        $dateValue = \XLite\Core\Request::getInstance()->$fieldName;

        if (!isset($dateValue)) {
            $nameDay   = $fieldName . 'Day';
            $nameMonth = $fieldName . 'Month';
            $nameYear  = $fieldName . 'Year';

            if (
                isset(\XLite\Core\Request::getInstance()->$nameMonth)
                && isset(\XLite\Core\Request::getInstance()->$nameDay)
                && isset(\XLite\Core\Request::getInstance()->$nameYear)
            ) {
                $dateValue = mktime(
                    $isEndDate ? 23 : 0,
                    $isEndDate ? 59 : 0,
                    $isEndDate ? 59 : 0,
                    \XLite\Core\Request::getInstance()->$nameMonth,
                    \XLite\Core\Request::getInstance()->$nameDay,
                    \XLite\Core\Request::getInstance()->$nameYear
                );
            }
        }

        return $dateValue;
    }
    // }}}
}