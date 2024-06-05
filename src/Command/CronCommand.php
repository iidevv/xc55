<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Event\Task\CronEvent;

class CronCommand extends Command
{
    protected static $defaultName = 'xcart:cron';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        $this->setDescription('Launches scheduled task execution (cron). Options and arguments are not supported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = new CronEvent();
        $event->setStartTime(time());
        $event->setMemoryLimitIni(\XLite\Core\Converter::convertShortSize(ini_get('memory_limit') ?: '64M'));
        $event->setOutput($output);

        $actionTime = microtime(true);

        $this->eventDispatcher->dispatch($event, CronEvent::NAME);

        $duration = microtime(true) - $actionTime;
        $micro = $duration - floor($duration);

        $output->writeln(
            PHP_EOL . 'Execution time: '
            . gmdate('H:i:s', floor($duration))
            . '.' . sprintf('%04d', $micro * 10000) . ' sec.'
        );

        return Command::SUCCESS;
    }
}
