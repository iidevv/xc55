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
use XCart\Doctrine\Migration\MigrationInterface;
use XCart\Exception\UpdateMigrationException;

final class GetUpdateMigrationCommand extends Command
{
    protected static $defaultName = 'xcart:service:get-update-migration';

    private MigrationInterface $migration;

    public function __construct(
        MigrationInterface $migration
    ) {
        parent::__construct();

        $this->migration = $migration;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate sql schema for tables update (used in rebuild)')
            ->addArgument('enabledModuleTables', InputArgument::REQUIRED, 'Array of table names in JSON')
            ->addArgument('disabledModuleTables', InputArgument::REQUIRED, 'Array of table names in JSON')
            ->addArgument('disabledModuleColumns', InputArgument::REQUIRED, 'Array of column names in JSON');
    }

    /**
     * @throws UpdateMigrationException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $queries = $this->migration->getUpdateMigration(
                json_decode($input->getArgument('enabledModuleTables'), false, 512, JSON_THROW_ON_ERROR),
                json_decode($input->getArgument('disabledModuleTables'), false, 512, JSON_THROW_ON_ERROR),
                json_decode($input->getArgument('disabledModuleColumns'), true, 512, JSON_THROW_ON_ERROR)
            );

            $output->writeln(Yaml::dump($queries, 5));
        } catch (\Throwable $e) {
            throw new UpdateMigrationException($e->getMessage(), $e->getCode(), $e);
        }

        return Command::SUCCESS;
    }
}
