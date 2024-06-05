<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\SyncInventoryCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use XCart\Container;
use XLite\Core\Config;

class SyncInventoryDispatcher
{
    protected ExportMessage $message;

    public function __construct()
    {
        $lastSyncTime = Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_items_last_sync_time;
        $modifiedAfter = gmdate('Y-m-d\TH:i:s\Z', $lastSyncTime);
        $modifiedBefore = gmdate('Y-m-d\TH:i:s\Z');

        /** @var SyncInventoryCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\SyncInventoryCommandFactory') : null;
        $command        = $commandFactory->createCommand($modifiedAfter, $modifiedBefore);
        $this->message  = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
