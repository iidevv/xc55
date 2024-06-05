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
use Symfony\Component\Yaml\Yaml;
use XCart\Doctrine\ModuleStructures;

final class GetProtectedStructuresCommand extends Command
{
    protected static $defaultName = 'xcart:service:get-protected-structures';

    private ModuleStructures $moduleStructures;

    private string $sourcePath;

    public function __construct(
        string $sourcePath,
        ModuleStructures $moduleStructures
    ) {
        parent::__construct();

        $this->sourcePath       = $sourcePath;
        $this->moduleStructures = $moduleStructures;
    }

    protected function configure(): void
    {
        $this->addArgument('moduleIds', InputArgument::REQUIRED | InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->moduleStructures->setSourceRoot("{$this->sourcePath}/");

        $result = [];
        foreach ($input->getArgument('moduleIds') as $moduleId) {
            $result[$moduleId] = $this->moduleStructures->getModuleStructures($moduleId);
        }

        $output->writeln(Yaml::dump($result, 5));

        return Command::SUCCESS;
    }
}
