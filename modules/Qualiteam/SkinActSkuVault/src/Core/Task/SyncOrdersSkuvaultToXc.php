<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Task;

use Qualiteam\SkinActSkuVault\Core\Dispatcher\PullOrdersDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XLite\Core\Config;
use XLite\Core\Task\Base\Periodic;

class SyncOrdersSkuvaultToXc extends Periodic
{
    /**
     * @var mixed|null
     */
    protected ?MessageBusInterface $bus;

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return static::t('Sync orders from SkuVault to X-Cart');
    }

    /**
     * @inheritDoc
     */
    protected function runStep()
    {
        if ((int)Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_orders_enable_sync === 1) {
            $dispatcher = new PullOrdersDispatcher();
            $message    = $dispatcher->getMessage();
            $this->bus  = Container::getContainer() ? Container::getContainer()->get('messenger.default_bus') : null;
            $this->bus->dispatch($message);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getPeriod()
    {
        return static::INT_5_MIN;
    }
}
