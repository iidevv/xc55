<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use XCart\Doctrine\Migration\MigrationInterface;
use XCart\Exception\CreateMigrationException;

final class GetCreateMigrationCommand extends Command
{
    protected static $defaultName = 'xcart:service:get-create-migration';

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
            ->setDescription('Generate sql schema for tables creation (used in install routine)');
    }

    /**
     * @throws CreateMigrationException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $queries = $this->migration->getCreateMigration();
        } catch (\Throwable $e) {
            throw new CreateMigrationException($e->getMessage(), $e->getCode(), $e);
        }

        $output->writeln(Yaml::dump($queries, 5));

        return Command::SUCCESS;
    }
}
