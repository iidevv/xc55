<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\PullOrdersCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use XCart\Container;
use XLite\Core\Config;
use XLite\Core\Converter;

class PullOrdersDispatcher
{
    const DATES_INTERVAL = 7 * 24 * 60 * 60;

    protected ExportMessage $message;

    public function __construct()
    {
        $lastSyncTime = Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_sales_last_sync_time;
        $now = Converter::time();

        if ($lastSyncTime) {
            $afterTime = $lastSyncTime;
        } else {
            $afterTime = 0;
        }

        if (
            empty($afterTime)
            || ($now - $afterTime) > self::DATES_INTERVAL
        ) {
            $afterTime = $now - self::DATES_INTERVAL;
        }

        $fromDate = gmdate('Y-m-d\TH:i:s\Z', $afterTime);
        $toDate = gmdate('Y-m-d\TH:i:s\Z', $now);

        /** @var PullOrdersCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\PullOrdersCommandFactory') : null;
        $command        = $commandFactory->createCommand($fromDate, $toDate);
        $this->message  = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
