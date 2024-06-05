<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Controller\Admin;

use Qualiteam\SkinActSkuVault\Core\Dispatcher\CheckInventoryDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\CreateProductsDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\SyncInventoryDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\PullOrdersDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\PushOrdersDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\UpdateProductsDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\UpdateVariantsDispatcher;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XLite\Controller\Admin\AAdmin;
use XLite\Core\Config;
use XLite\Core\Converter;

class SkuvaultCommands extends AAdmin
{
    protected ?MessageBusInterface $bus;
    protected ExportMessage        $message;

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->bus = Container::getContainer() ? Container::getContainer()->get('messenger.default_bus') : null;
    }

    protected function doActionCreateProducts()
    {
        $dispatcher = new CreateProductsDispatcher();
        $message    = $dispatcher->getMessage();

        $this->bus->dispatch($message);

        $this->setReturnURL(Converter::buildURL(\Qualiteam\SkinActSkuVault\View\Tabs\SkuVault::TAB_PRODUCTS));
    }

    protected function doActionSyncInventory()
    {
        $dispatcher = new SyncInventoryDispatcher();
        $message    = $dispatcher->getMessage();

        $this->bus->dispatch($message);

        $this->setReturnURL(Converter::buildURL(\Qualiteam\SkinActSkuVault\View\Tabs\SkuVault::TAB_PRODUCTS));
    }

    protected function doActionUpdateProducts()
    {
        $dispatcher = new UpdateProductsDispatcher();
        $this->bus->dispatch($dispatcher->getMessage());

        $dispatcherVariants = new UpdateVariantsDispatcher();
        $this->bus->dispatch($dispatcherVariants->getMessage());

        $this->setReturnURL(Converter::buildURL(\Qualiteam\SkinActSkuVault\View\Tabs\SkuVault::TAB_PRODUCTS));
    }

    protected function doActionCheckInventory()
    {
        $dispatcher = new CheckInventoryDispatcher();
        $message    = $dispatcher->getMessage();

        $this->bus->dispatch($message);

        $this->setReturnURL(Converter::buildURL(\Qualiteam\SkinActSkuVault\View\Tabs\SkuVault::TAB_PRODUCTS));
    }

    /**
     * Sync orders from X-Cart to SkuVault
     *
     * @return void
     */
    protected function doActionSyncOrdersXcToSkuvault()
    {
        if ((int)Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_orders_enable_sync === 1) {
            $dispatcher = new PushOrdersDispatcher();
            $message    = $dispatcher->getMessage();
            $this->bus->dispatch($message);
        }

        $this->setReturnURL(Converter::buildURL(\Qualiteam\SkinActSkuVault\View\Tabs\SkuVault::TAB_ORDERS));
    }

    /**
     * Sync orders from SkuVault to X-Cart
     *
     * @return void
     */
    protected function doActionSyncOrdersSkuvaultToXc()
    {
        if ((int)Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_orders_enable_sync === 1) {
            $dispatcher = new PullOrdersDispatcher();
            $message    = $dispatcher->getMessage();
            $this->bus->dispatch($message);
        }

        $this->setReturnURL(Converter::buildURL(\Qualiteam\SkinActSkuVault\View\Tabs\SkuVault::TAB_ORDERS));
    }
}
