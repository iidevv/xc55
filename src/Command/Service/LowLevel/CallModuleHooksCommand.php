<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service\LowLevel;

use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XCart\Domain\HookManagerDomain;
use XCart\Exception\HookManagerException;

final class CallModuleHooksCommand extends Command
{
    protected static $defaultName = 'xcart:service:ll:call-module-hooks';

    private HookManagerDomain $hookManager;

    public function __construct(HookManagerDomain $hookManager)
    {
        parent::__construct();

        $this->hookManager = $hookManager;
    }

    protected function configure(): void
    {
        $this->addArgument('hooks', InputArgument::REQUIRED);
    }

    /**
     * @throws HookManagerException
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $allHookTypes = json_decode($input->getArgument('hooks'), true, 512, JSON_THROW_ON_ERROR);

        $count = 0;

        foreach ($allHookTypes as $hookType => $hooks) {
            foreach ($hooks as $hook) {
                $count += $this->hookManager->runHook([
                    'moduleId'    => $hook['moduleId'],
                    'hookType'    => $hookType,
                    'versionFrom' => $hook['versionFrom'] ?? '',
                    'versionTo'   => $hook['versionTo'] ?? '',
                ]);
            }
        }

        $output->writeln("{$count} hook entities was called");

        return Command::SUCCESS;
    }
}
