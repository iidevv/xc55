<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Messenger\Message\Dummy;
use XCart\Messenger\Message\LongDummy;

final class ScheduleDummyCommand extends Command
{
    protected static $defaultName = 'xcart:messenger:schedule-dummy';

    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct(static::$defaultName);

        $this->bus = $bus;
    }

    protected function configure(): void
    {
        $this
            ->addOption('long', 'l', InputOption::VALUE_NONE, 'Schedule long one')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'Count to schedule');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('long') === true) {
            for ($i = 0; $i < $input->getOption('count'); $i++) {
                $this->bus->dispatch(new LongDummy());
            }
        } else {
            for ($i = 0; $i < $input->getOption('count'); $i++) {
                $this->bus->dispatch(new Dummy());
            }
        }

        return Command::SUCCESS;
    }
}
