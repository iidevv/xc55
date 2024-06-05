<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\ColorSwatches\LifetimeHook;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use XCart\Doctrine\FixtureLoader;
use XLite\Core\Database;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    private EntityManagerInterface $entityManager;

    public function __construct(FixtureLoader $fixtureLoader, EntityManagerInterface $entityManager)
    {
        $this->fixtureLoader = $fixtureLoader;
        $this->entityManager = $entityManager;
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'QSL/ColorSwatches/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    /**
     * Rename table
     *
     * @throws Exception|Exception|\Doctrine\DBAL\Exception
     */
    public function onUpgradeTo5507(): void
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        $allTables     = $schemaManager->listTableNames();

        // 1) define old table name
        $oldTableName = array_filter($allTables, static fn($tableName) => (bool)preg_match('/_sql_color_swtach_translations$/i', $tableName));
        if (!$oldTableName) {
            // xlite_sql_color_swtach_translations doesn't exist
            return;
        }
        $oldTableName = reset($oldTableName); // xlite_sql_color_swtach_translations

        // 2) define new table name and drop it if needed
        $newTableName   = str_ireplace('_sql_color_swtach_translations', '_qsl_color_swatch_translations', $oldTableName); // xlite_qsl_color_swatch_translations
        $newTableExists = in_array($newTableName, $allTables);
        if ($newTableExists) {
            if ((int)Database::getEM()->getConnection()->executeQuery("SELECT COUNT(*) FROM $newTableName")->fetchOne() > 0) {
                // There is data in the new table, we cannot do anything
                return;
            }
            // drop the new table before rename
            $schemaManager->dropTable($newTableName);
        }

        // 3) finally
        $schemaManager->renameTable($oldTableName, $newTableName);
    }
}
