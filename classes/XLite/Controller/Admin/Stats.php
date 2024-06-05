<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Converter;

/**
 * Store statistics page controller
 */
class Stats extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Time params
     */
    public const P_TODAY = 'today';
    public const P_WEEK  = 'week';
    public const P_MONTH = 'month';
    public const P_YEAR  = 'year';
    public const P_ALL   = 'all';

    /**
     * Statistics data
     *
     * @var array
     */
    protected $stats;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Reports');
    }

    /**
     * Prepare statistics table
     *
     * @return array
     */
    public function getStats()
    {
        if ($this->stats === null) {
            $this->stats = $this->initStats();
        }

        return $this->stats;
    }

    /**
     * Get column headings
     *
     * @return array
     */
    public function getColumnTitles()
    {
        return [
            self::P_TODAY => 'Today',
            self::P_WEEK  => 'This week',
            self::P_MONTH => 'This month',
            self::P_YEAR  => 'This year',
            self::P_ALL   => 'All time',
        ];
    }

    /**
     * Get row headings
     *
     * @return array
     */
    public function getRowTitles()
    {
        return [];
    }

    /**
     * Get column heading
     *
     * @param string $column Column identificator
     *
     * @return array|string
     */
    public function getColumnTitle($column)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getColumnTitles(), $column);
    }

    /**
     * Get row heading
     *
     * @param string $row Row identificator
     *
     * @return array|string
     */
    public function getRowTitle($row)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getRowTitles(), $row);
    }

    // {{{ Common functions

    /**
     * Get rows for statistics table
     *
     * @return array
     */
    public function getStatsRows()
    {
        return [];
    }

    /**
     * Get columns for statistics table
     *
     * @return array
     */
    public function getStatsColumns()
    {
        return $this->getTimeIntervals();
    }

    /**
     * Get currencies
     *
     * @return array
     */
    public function getCurrencies()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Currency')->findUsed();
    }

    /**
     * Get currency from request
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return \XLite\Core\Request::getInstance()->currency
            ? \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->findOneBy(['currency_id' => \XLite\Core\Request::getInstance()->currency])
            : \XLite::getInstance()->getCurrency();
    }

    /**
     * Initialize table matrix
     *
     * @return array
     */
    protected function initStats()
    {
        return array_fill_keys(
            $this->getStatsRows(),
            array_fill_keys($this->getStatsColumns(), 0)
        );
    }

    /**
     * Get search condition
     *
     * @param string $interval Time interval OPTIONAL
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition($interval = null)
    {
        $cnd = new \XLite\Core\CommonCell();

        $intervals = $this->getTimeIntervals();

        $cnd->date = [
            $this->getStartTime($interval ?: array_pop($intervals)),
            LC_START_TIME
        ];

        $cnd->currency = $this->getCurrency();

        return $cnd;
    }

    /**
     * Get search data
     *
     * @return array
     */
    protected function getData()
    {
        return null;
    }

    // }}}

    // {{{ Time intervals operations

    /**
     * Time intervals
     *
     * @return array
     */
    protected function getTimeIntervals()
    {
        return [
            self::P_TODAY,
            self::P_WEEK,
            self::P_MONTH,
            self::P_YEAR,
            self::P_ALL,
        ];
    }

    /**
     * Get timestamp of current day start
     *
     * @param string $interval Interval OPTIONAL
     *
     * @return integer
     */
    protected function getStartTime($interval = self::P_ALL)
    {
        $methodName = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($interval) . 'StartTime';

        return method_exists($this, $methodName)
            ? call_user_func([$this, $methodName])
            : $this->getDefaultStartTime();
    }

    /**
     * Get timestamp of current day start
     *
     * @return integer
     */
    protected function getTodayStartTime()
    {
        $time = Converter::convertTimeToUser(Converter::time());
        return Converter::convertTimeToServer(Converter::getDayStart($time));
    }

    /**
     * Get timestamp of current week start
     *
     * @return integer
     */
    protected function getWeekStartTime()
    {
        $time = Converter::convertTimeToUser(Converter::time());
        return $this->getTodayStartTime() - (date('w', $time) * 86400);
    }

    /**
     * Get timestamp of current month start
     *
     * @return integer
     */
    protected function getMonthStartTime()
    {
        $time = Converter::convertTimeToUser(Converter::time());
        return Converter::convertTimeToServer(mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
    }

    /**
     * Get timestamp of current year start
     *
     * @return integer
     */
    protected function getYearStartTime()
    {
        $time = Converter::convertTimeToUser(Converter::time());
        return Converter::convertTimeToServer(mktime(0, 0, 0, 1, 1, date('Y', $time)));
    }

    /**
     * Get start time for all dates condition
     *
     * @return integer
     */
    protected function getAllStartTime()
    {
        return 0;
    }

    /**
     * Get start time for all dates condition
     *
     * @return integer
     */
    protected function getDefaultStartTime()
    {
        return 0;
    }

    // }}}
}
