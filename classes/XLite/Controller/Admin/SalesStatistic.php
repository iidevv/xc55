<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\View\Order\Statistics\SalesStatistic as SalesStatisticWidget;

/**
 * Sales statistic controller
 */
class SalesStatistic extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    protected function doActionGetPeriodStatistic()
    {
        $this->setPureAction(true);
        $this->setSuppressOutput(true);
        $this->silent = true;

        [$todayTime, $startTime, $yoyStartTime, $yoyEndTime] = $this->getTimesForStatistic();

        if ($startTime === 0) {
            print ('');
            return;
        }

        $currency = \XLite::getInstance()->getCurrency();

        $todayStats = Database::getRepo('XLite\Model\Order')->getOrderStats($todayTime);
        $todayStats['orders_count'] = $todayStats['orders_count'] ?: 0;
        $todayStats['orders_total'] = $currency->roundValue($todayStats['orders_total'] ?: 0);
        $todayStats['orders_total_formatted'] = \XLite\View\AView::formatPrice($todayStats['orders_total'], $currency);

        $yoyStats = Database::getRepo('XLite\Model\Order')->getOrderStats($yoyStartTime, $yoyEndTime);
        $yoyStats['orders_count'] = $yoyStats['orders_count'] ?: 0;
        $yoyStats['orders_total'] = $currency->roundValue($yoyStats['orders_total'] ?: 0);

        $endTime = $todayTime - 1;
        $periodStats = Database::getRepo('XLite\Model\Order')->getGroupedOrderStats($startTime, $endTime);
        $periodStats = $this->preparePeriodStats($periodStats, $startTime, $endTime);

        $periodRevenue = 0;
        $periodOrders = 0;
        foreach ($periodStats as $stat) {
            $periodOrders += $stat['orders_count'];
            $periodRevenue += $stat['orders_total'];
        }

        $data = [
            'todayData' => $todayStats,
            'periodYoY' => null,
            'periodRevenue' => \XLite\View\AView::formatPrice($periodRevenue, $currency),
            'periodOrders' => $periodOrders,
            'periodData' => $periodStats,
            'icuDateFormat' => Converter::getDateFormatsByStrftimeFormat()['icuFormat'],
        ];

        if ($yoyStats['orders_total'] > 0) {
            $data['periodYoY'] = round(
                ($periodRevenue - $yoyStats['orders_total']) / $yoyStats['orders_total'] * 100,
                1
            );
        }

        $this->printAJAX($data);
    }

    /**
     * @param array $stats
     * @param       $startTime
     * @param       $endTime
     *
     * @return array
     */
    protected function preparePeriodStats(array $stats, $startTime, $endTime)
    {
        $statsTimestamps = array_map(static function ($stat) {
            return $stat['group_timestamp'];
        }, $stats);
        $stats = array_combine($statsTimestamps, $stats);

        $currency = \XLite::getInstance()->getCurrency();

        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            if (isset($stats[$i])) {
                $stats[$i]['orders_total'] = $currency->roundValue($stats[$i]['orders_total']);
                $stats[$i]['orders_total_formatted'] = \XLite\View\AView::formatPrice($stats[$i]['orders_total'], $currency);
            } else {
                $stats[$i] = [
                    'orders_total' => 0,
                    'orders_total_formatted' => \XLite\View\AView::formatPrice(0, $currency),
                    'orders_count' => 0,
                    'group_timestamp' => $i,
                ];
            }
        }

        ksort($stats, SORT_NUMERIC);

        return array_values($stats);
    }

    /**
     * @return array
     */
    protected function getTimesForStatistic()
    {
        $period = Request::getInstance()->period;
        $todayTime = strtotime('today');
        $yoyEndTime = mktime(
            0,
            0,
            0,
            date('m', $todayTime),
            date('d', $todayTime),
            date('Y', $todayTime) - 1
        );

        switch ($period) {
            case SalesStatisticWidget::PERIOD_7_DAYS:
                $startTime = $todayTime - 7 * 86400;
                $yoyStartTime = $yoyEndTime - 7 * 86400;
                break;

            case SalesStatisticWidget::PERIOD_30_DAYS:
                $startTime = $todayTime - 30 * 86400;
                $yoyStartTime = $yoyEndTime - 30 * 86400;
                break;

            case SalesStatisticWidget::PERIOD_12_MONTHS:
                $startTime = mktime(
                    0,
                    0,
                    0,
                    date('m', $todayTime),
                    date('d', $todayTime),
                    date('Y', $todayTime) - 1
                ) - 86400;
                $yoyStartTime = mktime(
                    0,
                    0,
                    0,
                    date('m', $yoyEndTime),
                    date('d', $yoyEndTime),
                    date('Y', $yoyEndTime) - 1
                ) - 86400;
                break;

            default:
                return [0, 0, 0, 0];
        }

        $todayTime      = Converter::convertTimeToServer($todayTime);
        $startTime      = Converter::convertTimeToServer($startTime);
        $yoyStartTime   = Converter::convertTimeToServer($yoyStartTime);
        $yoyEndTime     = Converter::convertTimeToServer($yoyEndTime);

        return [$todayTime, $startTime, $yoyStartTime, $yoyEndTime - 1];
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['get_period_statistic']);
    }
}
