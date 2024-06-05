<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\Migration;

/**
 * Is used as realization for empty modules' classes that pass $preservedTables from their services.yaml
 */
class MigrationTablesInjector implements MigrationInterface
{
    private MigrationInterface $migration;

    private array $preservedTables;

    public function __construct(MigrationInterface $migration, array $preservedTables)
    {
        $this->migration       = $migration;
        $this->preservedTables = $preservedTables;
    }

    public function getCreateMigration(): array
    {
        return $this->migration->getCreateMigration();
    }

    public function getUpdateMigration(
        array $enabledModuleTables,
        array $disabledModuleTables,
        array $disabledModuleColumns
    ): array {
        return $this->migration->getUpdateMigration(
            $enabledModuleTables,
            [...$disabledModuleTables, ...$this->preservedTables],
            $disabledModuleColumns
        );
    }
}
