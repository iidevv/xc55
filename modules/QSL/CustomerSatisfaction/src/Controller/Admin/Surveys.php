<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Controller\Admin;

/**
 * Surveys controller
 */
class Surveys extends \XLite\Controller\Admin\AAdmin
{
    // {{{ Search

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Surveys');
    }

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

    /**
     * Get date condition parameter (start or end)
     *
     * @param bool $start Start date flag, otherwise - end date  OPTIONAL
     *
     * @return mixed
     */
    public function getDateCondition($start = true)
    {
        $dates = $this->getCondition(\QSL\CustomerSatisfaction\Model\Repo\Survey::SEARCH_EMAIL_DATE);
        $n = ($start === true) ? 0 : 1;

        $date = $dates[$n] ?? LC_START_TIME;
        if ($start && $date == LC_START_TIME) {
            $date -= 86400 * 30;
        }

        return $date;
    }

    /**
     * Get date value for search params
     *
     * @param string  $fieldName Field name (prefix)
     * @param bool $isEndDate End date flag OPTIONAL
     *
     * @return integer
     */
    public function getDateValue($fieldName, $isEndDate = false)
    {
        $dateValue = \XLite\Core\Request::getInstance()->$fieldName;

        if (isset($dateValue)) {
            $timeValue = $isEndDate ? '23:59:59' : '0:0:0';
            $dateValue = intval(strtotime($dateValue . ' ' . $timeValue));
        } else {
            $dateValue = time();
        }

        return $dateValue;
    }

    /**
     * Save search conditions
     */
    protected function doActionSearch()
    {
        $cellName = \QSL\CustomerSatisfaction\View\ItemsList\Model\Survey::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        // Prepare dates

        $this->startDate = $this->getDateValue('startDate');
        $this->endDate   = $this->getDateValue('endDate', true);

        if (
            $this->startDate === 0
            || $this->endDate === 0
            || $this->startDate > $this->endDate
        ) {
            $date = getdate(time());

            $this->startDate = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
            $this->endDate   = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
        }

        $searchParams = $this->getConditions();

        foreach (
            \QSL\CustomerSatisfaction\View\ItemsList\Model\Survey::getSearchParams() as $requestParam
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
        $cellName = \QSL\CustomerSatisfaction\View\ItemsList\Model\Survey::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];

            $now = time();
            $startDate = $now - 2592000; // One month

            $searchParams['dateRange'] =  date('Y-m-d', $startDate) . ' ~ ' . date('Y-m-d', $now);
            \XLite\Core\Session::getInstance()->$cellName =  $searchParams;
        }

        return $searchParams;
    }

    // }}}
}
