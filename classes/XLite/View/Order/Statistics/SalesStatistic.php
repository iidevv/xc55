<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Statistics;

use XCart\Extender\Mapping\ListChild;

/**
 * Sales statistic (used on Dashboard page)
 *
 * @ListChild (list="dashboard-sidebar", weight="100", zone="admin")
 */
class SalesStatistic extends \XLite\View\AView implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    public const PERIOD_7_DAYS    = 'period_7_days';
    public const PERIOD_30_DAYS   = 'period_30_days';
    public const PERIOD_12_MONTHS = 'period_12_months';

    /**
     * Add widget specific CSS file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Add widget specific JS-file
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = ['url' => 'https://www.gstatic.com/charts/loader.js'];
        $list[] = $this->getDir() . '/statistic_store.js';
        $list[] = $this->getDir() . '/statistic_daily.js';
        $list[] = $this->getDir() . '/statistic_period.js';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'order/statistics/sales_statistic';
    }

    /**
     * @return string
     */
    protected function getDefaultPeriod()
    {
        return static::PERIOD_30_DAYS;
    }

    /**
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Revenue' => static::t('Revenue'),
            'Orders'  => static::t('Orders'),
        ];
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }
}
