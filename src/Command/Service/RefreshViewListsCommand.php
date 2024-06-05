<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XCart\Operation\Service\ViewListRefresh;

final class RefreshViewListsCommand extends Command
{
    protected static $defaultName = 'xcart:service:refresh-view-lists';

    private ViewListRefresh $viewListRefresh;

    public function __construct(
        ViewListRefresh $viewListRefresh
    ) {
        parent::__construct();

        $this->viewListRefresh = $viewListRefresh;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates the ViewLists.')
            ->setHelp('Updates the ViewLists. Actual data is extracted from the files, and ThemeTweaker and skin updates are applied and saved in the database. Options and arguments are not supported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ($this->viewListRefresh)();

        return Command::SUCCESS;
    }
}
