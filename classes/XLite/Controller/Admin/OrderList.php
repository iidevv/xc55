<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Lock\FileLock;
use XLite\Controller\Features\SearchByFilterTrait;

/**
 * Orders list controller
 */
class OrderList extends \XLite\Controller\Admin\AAdmin
{
    use SearchByFilterTrait;

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Orders');
    }

    // {{{ Search

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: 'XLite\View\ItemsList\Model\Order\Admin\Search';
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

    /**
     * Get date condition parameter (start or end)
     *
     * @param bool $start Start date flag, otherwise - end date  OPTIONAL
     *
     * @return mixed
     */
    public function getDateCondition($start = true)
    {
        $dates = $this->getCondition(\XLite\Model\Repo\Order::P_DATE);
        $n = ($start === true) ? 0 : 1;

        return $dates[$n] ?? null;
    }

    /**
     * Common prefix for editable elements in lists
     *
     * NOTE: this method is requered for the GetWidget and AAdmin classes
     * TODO: after the multiple inheritance should be moved to the AAdmin class
     *
     * @return string
     */
    public function getPrefixPostedData()
    {
        return 'data';
    }

    // }}}

    // {{{ Actions

    /**
     * Search by customer
     */
    protected function doActionSearchByCustomer()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [
            'substring' => \XLite\Core\Request::getInstance()->substring,
            'profileId' => (int) \XLite\Core\Request::getInstance()->profileId,
        ];

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }

    /**
     * doActionUpdate
     */
    protected function doActionUpdateItemsList()
    {
        $changes = $this->getOrdersChanges();

        parent::doActionUpdateItemsList();

        $updateRecent = [];
        foreach ($changes as $orderId => $change) {
            if (!empty($change['paymentStatus']) || !empty($change['shippingStatus'])) {
                $updateRecent[$orderId] = ['recent' => 0];
            }
            \XLite\Core\OrderHistory::getInstance()->registerOrderChanges($orderId, $change);
        }

        if (!empty($updateRecent)) {
            \XLite\Core\Database::getRepo('XLite\Model\Order')->updateInBatchById($updateRecent);
        }
    }

    /**
     * Do action delete
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XLite\Model\Order')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo(
                'Orders has been deleted successfully'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the orders first');
        }
    }

    /**
     * Clear search conditions for searchTotal
     */
    protected function clearSearchTotalConditions()
    {
        $searchTotalSessionCell = \XLite\View\ItemsList\Model\Order\Admin\SearchTotal::getSessionCellName();
        \XLite\Core\Session::getInstance()->{$searchTotalSessionCell} = [];
    }

    /**
     * Clear search conditions
     */
    protected function doActionClearSearch()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        $this->clearSearchTotalConditions();

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }

    /**
     * Bulk payment status change
     */
    public function doActionChangePaymentStatus()
    {
        $statusToSet  = \XLite\Core\Request::getInstance()->statusToSet;
        $select = array_filter(\XLite\Core\Request::getInstance()->select);

        $lock = new FileLock();
        $keyPayment = 'paymentBulkInProcess';
        $keyShipping = 'shippingBulkInProcess';
        if (!$lock->isRunning($keyShipping, false, 600)) {
            $lock->setRunning($keyPayment, 600);
            $this->changeStatusesBulk('paymentStatus', $statusToSet, $select);
            $lock->release($keyPayment);
        } else {
            \XLite\Core\TopMessage::addError('Another bulk change in progress, please try again later');
            $list = $this->getItemsList();
            if ($list) {
                $list->clearSavedData();
            }
        }
    }

    /**
     * Bulk fulfillment status change
     */
    public function doActionChangeFulfillmentStatus()
    {
        $statusToSet  = \XLite\Core\Request::getInstance()->statusToSet;
        $select = array_filter(\XLite\Core\Request::getInstance()->select);

        $lock = new FileLock();
        $keyPayment = 'paymentBulkInProcess';
        $keyShipping = 'shippingBulkInProcess';
        if (!$lock->isRunning($keyPayment, false, 600)) {
            $lock->setRunning($keyShipping, 600);
            $this->changeStatusesBulk('shippingStatus', $statusToSet, $select);
            $lock->release($keyShipping);
        } else {
            \XLite\Core\TopMessage::addError('Another bulk change in progress, please try again later');
            $list = $this->getItemsList();
            if ($list) {
                $list->clearSavedData();
            }
        }
    }

    /**
     * @param $statusType
     * @param $statusId
     * @param $select
     */
    protected function changeStatusesBulk($statusType, $statusId, $select)
    {
        if ($statusId && $select && is_array($select)) {
            $data = [];
            foreach ($select as $id => $value) {
                $data[$id] = [];
                $data[$id][$statusType] = $statusId;
                $data[$id]['_changed'] = true;
            }

            if ($data) {
                $dataPrefix = $this->getItemsList()->getDataPrefix();
                $_POST[$dataPrefix] = $data;
                \XLite\Core\Request::getInstance()->mapRequest();
                $this->doActionUpdateItemsList();
            }
        }
    }
    /**
     * Process 'no action'
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {
            // Clear stored search conditions
            \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];
            $this->clearSearchTotalConditions();
            $this->prepareSearchParams();

            // Get ItemsList widget
            $widget = new \XLite\View\ItemsList\Model\Order\Admin\Search();

            // Search for single order
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceof \XLite\Model\Order) {
                // Prepare redirect to order page
                $url = $this->buildURL('order', '', ['order_number' => $entity->getOrderNumber()]);
                $this->setReturnURL($url);
            }
        }
    }

    /**
     * Get search filter
     *
     * @return \XLite\Model\SearchFilter
     */
    public function getSearchFilter()
    {
        $filter = parent::getSearchFilter();

        if (!$filter && \XLite\Core\Request::getInstance()->filter_id == 'recent') {
            $searchParams = [
                \XLite\Model\Repo\Order::P_RECENT => 1,
                static::PARAM_SEARCH_FILTER_ID    => 'recent',
            ];

            $filter = new \XLite\Model\SearchFilter();
            $filter->setParameters($searchParams);
        }

        return $filter;
    }

    /**
     * Get currently used filter
     *
     * @return \XLite\Model\SearchFilter
     */
    public function getCurrentSearchFilter()
    {
        $filter = parent::getCurrentSearchFilter();

        if (!$filter) {
            $cellName = $this->getSessionCellName();
            $searchParams = \XLite\Core\Session::getInstance()->$cellName;
            if (
                isset($searchParams[static::PARAM_SEARCH_FILTER_ID])
                && $searchParams[static::PARAM_SEARCH_FILTER_ID] === 'recent'
            ) {
                $filter = new \XLite\Model\SearchFilter();
                $filter->setId('recent');
                $filter->setName(static::t('Awaiting processing'));
            }
        }

        return $filter;
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
     * Get order changes from request
     *
     * @return array
     */
    protected function getOrdersChanges()
    {
        $changes = [];

        foreach ($this->getPostedData() as $orderId => $data) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

            if (!$order) {
                continue;
            }

            foreach ($data as $name => $value) {
                if (in_array($name, ['status', '_changed'], true)) {
                    continue;
                }

                $dataFromOrder = $order->{'get' . ucfirst($name)}();

                if (
                    $dataFromOrder
                    && $dataFromOrder->getId() !== intval($value)
                ) {
                    $changes[$orderId][$name] = [
                        'old' => $dataFromOrder,
                        'new' => $value,
                    ];
                }
            }
        }

        return $changes;
    }

    // }}}
}
