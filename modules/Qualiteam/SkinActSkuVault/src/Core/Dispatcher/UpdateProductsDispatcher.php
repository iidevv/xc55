<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\UpdateProductsCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use XCart\Container;
use XLite\Core\Database;
use XLite\Model\Product;

class UpdateProductsDispatcher
{
    protected ExportMessage $message;

    public function __construct()
    {
        $productsIds = Database::getRepo(Product::class)->findProductIdsToSync();

        /** @var UpdateProductsCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\UpdateProductsCommandFactory') : null;
        $command        = $commandFactory->createCommand($productsIds);
        $this->message  = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
