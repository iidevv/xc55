<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Order\Statistic;

use XLite\View\Order\Statistics\SalesStatistic;

/**
 * Sales statistic period selector
 */
class SalesStatisticPeriod extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = [
            SalesStatistic::PERIOD_7_DAYS    => static::t('7 days'),
            SalesStatistic::PERIOD_30_DAYS   => static::t('30 days'),
            SalesStatistic::PERIOD_12_MONTHS => static::t('12 months'),
        ];

        return $result;
    }
}
