<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Command\LowLevel;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XLite\View\AResourcesContainer;

final class RefreshCacheTimestampCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'xcart:ll:refresh-cache-timestamp';

    protected function configure()
    {
        $this->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newCacheTimestamp = AResourcesContainer::refreshCacheTimestamp();
        $output->writeln("New cache timestamp is {$newCacheTimestamp}");

        return Command::SUCCESS;
    }
}
