<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Command;

use Qualiteam\SkinActSkuVault\Core\Dispatcher\CheckInventoryDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CheckInventoryCommand extends Command
{
    protected static $defaultName = 'SkinActSkuVault:CheckInventory';

    protected MessageBusInterface    $bus;
    protected CheckInventoryDispatcher $dispatcher;

    public function __construct(MessageBusInterface $bus, CheckInventoryDispatcher $dispatcher)
    {
        parent::__construct();
        $this->bus = $bus;
        $this->dispatcher = $dispatcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $this->dispatcher->getMessage();
        $this->bus->dispatch($message);

        return Command::SUCCESS;
    }
}
