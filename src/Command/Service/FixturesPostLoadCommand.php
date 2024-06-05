<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Event\Service\FixturesPostLoadEvent;

final class FixturesPostLoadCommand extends Command
{
    protected static $defaultName = 'xcart:service:post-fixtures-load';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Executes additional actions after data upload.')
            ->setHelp('Executes additional actions after data upload. Sometimes updating the database after the upload is required, e.g., fixing the category tree structure. Options and arguments are not supported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = new FixturesPostLoadEvent();
        $this->eventDispatcher->dispatch($event, FixturesPostLoadEvent::NAME);

        return Command::SUCCESS;
    }
}
