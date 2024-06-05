<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View;

use Includes\Utils\Module\Module;
use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\View\AView;

/**
 * Cron status view
 *
 * @ListChild (list="admin.center", zone="admin", weight="10")
 */
class CronStatus extends AView
{
    /**
     * Timestamp of the last completed cron
     */
    protected $time = 0;

    /**
     * Init view
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->time = intval(Config::getInstance()->Qualiteam->SkinActXPaymentsSubscriptions->cron_last_time_completed);
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'x_payments_subscription';
        $list[] = 'module';
        $list[] = 'product';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions/cron_status.twig';
    }

    /**
     * Get current module ID
     *
     * @return integer
     */
    public function getModuleID()
    {
        return Module::buildId('Qualiteam', 'SkinActXPaymentsSubscriptions');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $request = Request::getInstance();

        return parent::isVisible() && (
            ($request->target == 'product' && $request->page == 'subscription_plan')
            || ($request->target == 'module' && $request->moduleId == $this->getModuleID())
            || $request->target == 'x_payments_subscription'
        );
    }

    /**
     * Get formatted last execution time
     *
     * @return string
     */
    protected function getLastTime()
    {
        $time = $this->time;

        if ($time) {
            $time = \Xlite\Core\Converter::getInstance()->formatTime($time);
        }

        return $time;
    }

    /**
     * Get formatted last execution time
     *
     * @return string
     */
    protected function isLastTimeUnknown()
    {
        return 0 == $this->time;
    }

    /**
     * Check if time isnce last cron execution is Ok, i.e. smaller than one day
     *
     * @return int
     */
    protected function isTimePassedSinceLastCronOk()
    {
        return (Converter::now() - $this->time) < 86400;
    }

    /**
     * Link to the instruction for cron setup
     *
     * @return string
     */
    protected function getManualLink()
    {
        return 'http://kb.x-cart.com/display/XDD/Configuring+automated+execution+of+periodic+tasks+for+X-Payments+subscriptions';
    }
}
