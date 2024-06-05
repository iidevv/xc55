<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use XCart\Doctrine\Migration\MigrationInterface;
use XCart\Doctrine\Migration\SchemaComparator;

final class Migration implements MigrationInterface
{
    private EntityManagerInterface $entityManager;

    private SchemaTool $schemaTool;

    public function __construct(
        EntityManagerInterface $entityManager,
        SchemaTool $schemaTool
    ) {
        $this->entityManager = $entityManager;
        $this->schemaTool    = $schemaTool;
    }

    public function getCreateMigration(): array
    {
        $metadata = $this->getMetadata();
        $schema   = $this->schemaTool->getSchemaFromMetadata($metadata);

        return $this->getQueries($schema);
    }

    public function getUpdateMigration(
        array $enabledModuleTables,
        array $disabledModuleTables,
        array $disabledModuleColumns
    ): array {
        $fromSchema = $this->entityManager->getConnection()->createSchemaManager()->createSchema();
        $toSchema   = $this->schemaTool->getSchemaFromMetadata($this->getMetadata());

        // exclude tables without prefix
        $params      = $this->entityManager->getConnection()->getParams();
        $tablePrefix = $params['driverOptions']['table_prefix'] ?? '';
        foreach ($toSchema->getTables() as $fullTableName => $table) {
            if (strpos($table->getName(), $tablePrefix) !== 0) {
                $toSchema->dropTable($fullTableName);
            }
        }

        $platform   = $this->entityManager->getConnection()->getDatabasePlatform();
        $comparator = new SchemaComparator(
            new Comparator($platform),
            $disabledModuleTables,
            $disabledModuleColumns,
            $enabledModuleTables,
            $this->entityManager->getConnection()
        );

        $schemaDiff = $comparator->compare($fromSchema, $toSchema);

        return $this->getQueries($schemaDiff);
    }

    private function getMetadata(): array
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $result = [];
        foreach ($metadata as $data) {
            if ($this->isManaged($data)) {
                $result[] = $data;
            }
        }

        return $result;
    }

    private function isManaged(ClassMetadata $metadata): bool
    {
        return strpos($metadata->getName(), '\\Model\\') !== false;
    }

    /**
     * @param Schema|SchemaDiff $schema
     *
     * @return array
     */
    private function getQueries($schema): array
    {
        try {
            if ($platform = $this->entityManager->getConnection()->getDatabasePlatform()) {
                return $schema->toSql($platform);
            }
        } catch (DBALException $e) {
            // todo: throw exception
        }

        return [];
    }
}
