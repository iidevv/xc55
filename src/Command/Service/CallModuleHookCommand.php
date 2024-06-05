<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XCart\Domain\HookManagerDomain;

final class CallModuleHookCommand extends Command
{
    protected static $defaultName = 'xcart:service:call-module-hook';

    private HookManagerDomain $hookManager;

    public function __construct(HookManagerDomain $hookManager)
    {
        parent::__construct();

        $this->hookManager = $hookManager;
    }

    protected function configure(): void
    {
        $help = <<< HELP
Executes lifetime hooks in the X-Cart core and modules. Usually, hooks are executed automatically when a specific event takes place. However, sometimes manual hook execution may be required.  

<info>Arguments:</info>
    <fg=red;bg=gray;options=bold>hookType</> - A hook type. Possible values are init, install, enable, disable, remove, and upgrade.
    <fg=red;bg=gray;options=bold>moduleId</> - A module ID in the "{Author}-{Name}" format. For the X-Cart core, use "CDev-Core", and be warned that only upgrade hook type is supported.

<info>Options:</info> For an upgrade type hook, specify the applicable version range. Do mind that hooks corresponding with the start version of the range will not be executed.
    <fg=red;bg=gray;options=bold>--versionFrom</> - The start version in the "x.x.x.x" format
    <fg=red;bg=gray;options=bold>--versionTo</>   - The end version in the "x.x.x.x" format

<info>For example:</info> Suppose there are hooks to update the "XCExample-Sample" module to versions: "5.5.0.1", "5.5.0.2" и "5.5.0.3". You execute the following command:
    <fg=red;bg=gray;options=bold>./bin/console xcart:service:call-module-hook --versionFrom=5.5.0.1 --versionTo=5.5.0.3 upgrade XCExample-Sample</>

The "5.5.0.1" hooks will not be executed, while the "5.5.0.2" and "5.5.0.3" hooks will.
HELP;

        $this
            ->setDescription('Executes the core and add-ons lifetime hooks.')
            ->setHelp($help)
            ->addArgument('hookType', InputArgument::REQUIRED, 'A hook type. Possible values are init, install, enable, disable, remove, and upgrade.')
            ->addArgument('moduleId', InputArgument::REQUIRED, 'A module ID in the "{Author}-{Name}" format. For the X-Cart core, use "CDev-Core", and be warned that only upgrade hook type is supported.')
            ->addOption('versionFrom', null, InputOption::VALUE_REQUIRED, 'The start version in the "x.x.x.x" format')
            ->addOption('versionTo', null, InputOption::VALUE_REQUIRED, 'The end version in the "x.x.x.x" format');
    }

    /**
     * @throws \XCart\Exception\HookManagerException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->hookManager->runHook([
            'moduleId'    => $input->getArgument('moduleId'),
            'hookType'    => $input->getArgument('hookType'),
            'versionFrom' => $input->getOption('versionFrom') ?: '',
            'versionTo'   => $input->getOption('versionTo') ?: '',
        ]);

        $output->writeln("{$count} hook entities was called");

        $output->writeln('<info>OK</info>');

        return Command::SUCCESS;
    }
}
