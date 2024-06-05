<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadLabelsCommand extends Command
{
    protected static $defaultName = 'xcart:service:load-labels';

    protected function configure(): void
    {
        $help = <<< HELP
Uploads labels translation. It is used during the core and modules update. Only new translations are created, and existing labels translations are preserved unchanged.

<info>Arguments:</info>
    <fg=red;bg=gray;options=bold>yamlFiles</> - A comma-separated list of files for upload
HELP;

        $this
            ->setDescription('Uploads translation labels. It is used during the core and modules update.')
            ->setHelp($help)
            ->addArgument('yamlFiles', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'A comma-separated list of files for upload');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($input->getArgument('yamlFiles') as $file) {
            $output->writeln("Loading {$file}");
            \XLite\Core\Translation::getInstance()->loadLabelsFromYaml($file);
        }

        return Command::SUCCESS;
    }
}
