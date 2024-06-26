<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Orders statistics page controller
 */
class OrdersStats extends \XLite\Controller\Admin\Stats
{
    /**
     * Columns
     */
    public const P_PROCESSED  = 'processed';
    public const P_QUEUED     = 'queued';
    public const P_CANCELED   = 'canceled';
    public const P_DECLINED   = 'declined';
    public const P_TOTAL      = 'total';
    public const P_PAID       = 'paid';

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Sale statistics');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * TODO: Move to widget
     * getPageTemplate
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return 'orders_stats.twig';
    }

    /**
     * Get row headings
     *
     * @return array
     */
    public function getRowTitles()
    {
        return [
            self::P_PROCESSED  => 'Processed/Completed',
            self::P_QUEUED     => 'Queued',
            self::P_DECLINED   => 'Declined',
            self::P_CANCELED   => 'Canceled',
            self::P_TOTAL      => 'Total',
            self::P_PAID       => 'Paid',
        ];
    }

    /**
     * Status rows as row identificator => included statuses
     *
     * @return array
     */
    public function getStatusRows()
    {
        return [
            static::P_PROCESSED => [
                \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
            ],
            static::P_QUEUED => [
                \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            ],
            static::P_DECLINED => [
                \XLite\Model\Order\Status\Payment::STATUS_DECLINED,
            ],
            static::P_CANCELED => [
                \XLite\Model\Order\Status\Payment::STATUS_CANCELED,
            ],
            static::P_TOTAL => [
                \XLite\Model\Order\Status\Payment::STATUS_DECLINED,
                \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
                \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
            ],
            static::P_PAID => [
                \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
            ],
        ];
    }

    /**
     * Is totals row
     *
     * @param string $row Row identificator
     *
     * @return boolean
     */
    public function isTotalsRow($row)
    {
        return in_array(
            $row,
            [
                self::P_PAID,
                self::P_TOTAL,
            ]
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getStatsRows()
    {
        return array_keys($this->getStatusRows());
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

            foreach ($this->getStatsColumns() as $period) {
                $dataCount = $this->getDataCount($this->getStartTime($period));
                $dataTotal = $this->getDataTotal($this->getStartTime($period));

                foreach ($this->getStatusRows() as $row => $statuses) {
                    if ($this->isTotalsRow($row)) {
                        $this->stats[$row][$period] = $this->getDataByStatuses($dataTotal, $statuses);
                    } else {
                        $this->stats[$row][$period] = $this->getDataByStatuses($dataCount, $statuses);
                    }
                }
            }
        }

        return $this->stats;
    }

    /**
     * Returns statistics data
     *
     * @param integer $startTime Start time
     *
     * @return array
     */
    protected function getDataCount($startTime)
    {
        $condition = $this->defineGetDataCountCondition($startTime);

        return \XLite\Core\Database::getRepo('XLite\Model\Order')->getStatisticCount($condition);
    }

    /**
     * Returns statistic condition
     *
     * @param integer $startTime Start time
     *
     * @return \XLite\Core\CommonCell
     */
    protected function defineGetDataCountCondition($startTime)
    {
        $condition = new \XLite\Core\CommonCell();

        $condition->date = [
            $startTime,
            LC_START_TIME
        ];

        $condition->currency = $this->getCurrency();

        return $condition;
    }

    /**
     * Get data
     *
     * @param integer $startTime Start time
     *
     * @return array
     */
    protected function getDataTotal($startTime)
    {
        $condition = $this->defineGetDataTotalCondition($startTime);

        return \XLite\Core\Database::getRepo('XLite\Model\Order')->getStatisticTotal($condition);
    }

    /**
     * Returns statistic condition
     *
     * @param integer $startTime Start time
     *
     * @return \XLite\Core\CommonCell
     */
    protected function defineGetDataTotalCondition($startTime)
    {
        $condition = new \XLite\Core\CommonCell();

        $condition->date = [
            $startTime,
            LC_START_TIME
        ];

        $condition->currency = $this->getCurrency();

        return $condition;
    }

    /**
     * Get data by statuses
     *
     * @param array $data     Data
     * @param array $statuses Statuses
     *
     * @return integer|float
     */
    protected function getDataByStatuses($data, $statuses)
    {
        $result = 0;

        foreach ($data as $value) {
            if (in_array($value['code'], $statuses)) {
                $result += $value[1];
            }
        }

        return $result;
    }
}
