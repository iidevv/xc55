<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\CheckInventoryCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use Qualiteam\SkinActSkuVault\Model\SkuvaultItem;
use XCart\Container;
use XLite\Core\Database;

class CheckInventoryDispatcher
{
    protected ExportMessage $message;

    public function __construct()
    {
        Database::getRepo(SkuvaultItem::class)->deleteNotSyncedItems();
        $skuvaultItems = Database::getRepo(SkuvaultItem::class)->findAll();

        $skus = array_map(function (SkuvaultItem $skuvaultItem) {
            return $skuvaultItem->getSku();
        }, $skuvaultItems);

        /** @var CheckInventoryCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\CheckInventoryCommandFactory') : null;
        $command        = $commandFactory->createCommand($skus);
        $this->message  = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
