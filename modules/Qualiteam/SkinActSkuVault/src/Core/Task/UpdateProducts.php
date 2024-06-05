<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Task;

use Qualiteam\SkinActSkuVault\Core\Dispatcher\UpdateProductsDispatcher;
use Qualiteam\SkinActSkuVault\Core\Dispatcher\UpdateVariantsDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XLite\Core\Task\Base\Periodic;

class UpdateProducts extends Periodic
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
        return static::t('Update products [SkinActSkuVault]');
    }

    /**
     * @inheritDoc
     */
    protected function runStep()
    {
        $dispatcher = new UpdateProductsDispatcher();
        $message    = $dispatcher->getMessage();

        $this->bus = Container::getContainer() ? Container::getContainer()->get('messenger.default_bus') : null;
        $this->bus->dispatch($message);

        $dispatcherVariants = new UpdateVariantsDispatcher();
        $this->bus->dispatch($dispatcherVariants->getMessage());
    }

    /**
     * @inheritDoc
     */
    protected function getPeriod()
    {
        return static::INT_15_MIN;
    }
}
