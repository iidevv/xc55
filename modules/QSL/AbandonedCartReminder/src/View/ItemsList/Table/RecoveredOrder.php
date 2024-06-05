<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\ItemsList\Table;

use XLite\View\FormField\Input\Text\DateRange;

/**
 * ItemsList widget for Cart Recovery Statistics.
 */
class RecoveredOrder extends ATable
{
    /**
     * Widget param names
     */
    public const PARAM_DATE_RANGE = 'dateRange';

    /**
     * Cached period total.
     *
     * @var integer
     */
    protected $periodTotal = 0;

    /**
     * Return a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/AbandonedCartReminder/recovery_stats/style.less';

        return $list;
    }

    /**
     * Cached period number.
     *
     * @var integer
     */
    protected $periodNum   = 0;

    /**
     * Return an array of search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Order::SEARCH_DATE_RANGE => static::PARAM_DATE_RANGE,
        ];
    }

    /**
     * Return list title.
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Abandoned cart statistics for the period';
    }

    /**
     * Define columns structure.
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'date' => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/recovered_orders/cell.date.twig',
                static::COLUMN_NO_WRAP  => true,
            ],
            'orders' => [
                static::COLUMN_NAME     => static::t('Orders'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/recovered_orders/cell.orders.twig',
                static::COLUMN_NO_WRAP  => true,
            ],
            'total' => [
                static::COLUMN_NAME     => static::t('Month total'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/recovered_orders/cell.total.twig',
            ],
        ];
    }

    /**
     * Get container class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' recovered_orders';
    }

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
        ];
    }

    /**
     * Retrieves models and group them into table rows.
     *
     * @return void
     */
    protected function initRawPageData()
    {
        $this->rawPageData = [];

        $this->periodTotal = 0;
        $this->periodNum   = 0;

        $data = $this->retrieveData($this->getSearchCondition());

        $maxDate = 0;
        $currency = \XLite::getInstance()->getCurrency();

        foreach ($data as $order) {
            $date = \XLite\Core\Converter::convertDateToMonth($order->getDate());
            $currency = $order->getCurrency();

            if (!isset($minDate) || ($date < $minDate)) {
                $minDate = $date;
            }

            if ($date > $maxDate) {
                $maxDate = $date;
            }

            if (!isset($this->rawPageData[$date])) {
                $this->rawPageData[$date] = [
                    'date'     => $date,
                    'orders'   => [],
                    'total'    => 0,
                    'currency' => $currency,
                ];
            }
            $this->rawPageData[$date]['orders'][$order->getOrderId()] = $order;
            $this->rawPageData[$date]['total'] += $order->getTotal();

            $this->periodTotal += $order->getTotal();
            $this->periodNum++;
        }

        $this->fillRawDataWithEmptyMonths($currency);

        krsort($this->rawPageData);
    }

    /**
     * Fill empty months in the raw data.
     *
     * @param \XLite\Model\Currency $currency Currency OPTIONAL
     *
     * @return void
     */
    protected function fillRawDataWithEmptyMonths(\XLite\Model\Currency $currency = null)
    {
        // Fill empty months in the stats
        $dateRange = $this->getParam(static::PARAM_DATE_RANGE);
        [$startDate, $endDate] = DateRange::convertToArray($dateRange);

        $month = (int) date('n', $startDate);
        $year = (int) date('Y', $startDate);

        $maxDate = mktime(0, 0, 0, (int) date('n', $endDate), 1, (int) date('Y', $endDate));

        do {
            $date = mktime(0, 0, 0, $month, 1, $year);

            if (!isset($this->rawPageData[$date])) {
                $this->rawPageData[$date] = [
                    'date'     => $date,
                    'orders'   => [],
                    'total'    => 0,
                    'currency' => $currency,
                ];
            }

            $month++;
            if (11 < $month) {
                $month = 0;
                $year++;
            }
        } while ($date < $maxDate);
    }

    /**
     * Return models from the database.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function retrieveData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $this->getRepository()->search($cnd, $countOnly);
    }

    /**
     * Define repository name.
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order';
    }

    /**
     * Return params list to use for search.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result = $this->getRepository()->addConditionSearchRecovered($result);

        return $result;
    }

    /**
     * Get search values storage
     *
     * @param boolean $forceFallback Force fallback to session storage
     *
     * @return \XLite\View\ItemsList\ISearchValuesStorage
     */
    public static function getSearchValuesStorage($forceFallback = false)
    {
        $storage = parent::getSearchValuesStorage($forceFallback);

        $dates = $storage->getValue(static::PARAM_DATE_RANGE);
        if (!$dates) {
            $today = \XLite\Core\Converter::time();
            $todayMonth = date('n', $today);
            $todayYear  = date('Y', $today);

            $dates = DateRange::abcrConvertToString([
                mktime(0, 0, 0, $todayMonth, 1, $todayYear),
                mktime(0, 0, -1, $todayMonth + 1, 1, $todayYear),
            ]);
            $storage->setValue(static::PARAM_DATE_RANGE, $dates);
        }

        return $storage;
    }

    /**
     * Get sticky panel class
     */
    protected function getPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\StickyPanel\ItemsList\SingleSettingLink';
    }

    /**
     * Check - search panel is visible or not
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return true;
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\SearchPanel\Admin\CartRecoveryStats';
    }

    /**
     * Preprocess the order number before displaying it.
     *
     * @param string $number Order number
     *
     * @return string
     */
    protected function preprocessOrderNumber($number)
    {
        return '#' . str_repeat('0', 5 - strlen($number)) . $number;
    }

    /**
     * Format timestamp to "Month'Year" string.
     *
     * @param integer $timestamp Date
     *
     * @return string
     */
    protected function formatMonthYear($timestamp)
    {
        return date('F \'y', $timestamp);
    }

    /**
     * Whether to display the footer list.
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Return the total sum for the search period.
     *
     * @return float
     */
    protected function getPeriodTotal()
    {
        return $this->periodTotal;
    }

    /**
     * Return the total number of orders for the search period.
     *
     * @return integer
     */
    protected function getPeriodNum()
    {
        return $this->periodNum;
    }
}
