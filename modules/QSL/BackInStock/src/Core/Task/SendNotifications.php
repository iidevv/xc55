<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core\Task;

/**
 * Scheduled task that sends back in stock notification
 */
class SendNotifications extends \XLite\Core\Task\Base\Periodic
{
    public const INT_1_MIN     = 60;
    public const INT_5_MIN     = 300;
    public const INT_10_MIN    = 600;
    public const INT_15_MIN    = 900;
    public const INT_30_MIN    = 1800;
    public const INT_1_HOUR    = 3600;
    public const INT_2_HOURS   = 7200;
    public const INT_4_HOURS   = 14400;
    public const INT_6_HOURS   = 21600;
    public const INT_12_HOURS  = 43200;
    public const INT_1_DAY     = 86400;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return static::t('Send Back in stock notifications');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        [$sent, $bounced] = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
            ->sendNotifications();
        [$sentPrice, $bouncedPrice] = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
            ->sendNotifications();

        if ($sent || $bounced || $sentPrice || $bouncedPrice) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * @inheritdoc
     */
    protected function getPeriod()
    {
        return \XLite\Core\Config::getInstance()->QSL->BackInStock->updateInterval;
    }
}
