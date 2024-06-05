<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Schema\Comparator;

/**
 * Schema comparator that decorates standard Doctrine\DBAL\Schema\Comparator to bring the ability to protect specific tables, columns, foreign keys and indexes from removal.
 */
class SchemaComparator
{
    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * @var string[]
     */
    private $disabledTables;

    /**
     * @var string[][]
     */
    private $disabledColumns;

    /**
     * @var string[]
     */
    private $enabledTables;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * DisabledStructuresPreservingComparator constructor.
     *
     * @param Comparator $comparator
     * @param string[]   $disabledTables
     * @param string[]   $disabledColumns
     * @param string[]   $enabledTables
     * @param Connection $connection
     */
    public function __construct(Comparator $comparator, $disabledTables, $disabledColumns, $enabledTables, Connection $connection)
    {
        $this->comparator      = $comparator;
        $this->disabledTables  = $disabledTables;
        $this->disabledColumns = $disabledColumns;
        $this->enabledTables   = $enabledTables;
        $this->connection      = $connection;
    }

    /**
     * Returns a SchemaDiff object containing the differences between the schemas $fromSchema and $toSchema.
     *
     * The returned differences are returned in such a way that they contain the
     * operations to change the schema stored in $fromSchema to the schema that is
     * stored in $toSchema.
     *
     * @param Schema $fromSchema
     * @param Schema $toSchema
     *
     * @return SchemaDiff
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function compare(Schema $fromSchema, Schema $toSchema)
    {
        return $this->preserveTablesAndColumns(
            $this->comparator::compareSchemas($fromSchema, $toSchema)
        );
    }

    /**
     * Preserve ("un-remove") specific tables, columns, foreign keys and indexes that otherwise would be deleted by the default Doctrine's schema diffing procedure
     *
     * @param SchemaDiff $diff Schema diff object
     *
     * @return array
     */
    protected function preserveTablesAndColumns(SchemaDiff $diff)
    {
        if ($this->disabledTables) {
            // Do not drop disabled tables and their foreign keys:

            foreach ($diff->changedTables as $changedTable) {
                if (in_array($changedTable->name, $this->disabledTables)) {
                    $changedTable->addedForeignKeys   = [];
                    $changedTable->removedForeignKeys = [];
                }
            }

            foreach ($diff->newTables as $newTable) {
                if (in_array($newTable->getName(), $this->disabledTables)) {
                    foreach ($newTable->getForeignKeys() as $key) {
                        $newTable->removeForeignKey($key->getName());
                    }
                }
            }

            foreach ($diff->orphanedForeignKeys as $k => $key) {
                if (in_array($key->getLocalTableName(), $this->disabledTables)) {
                    unset($diff->orphanedForeignKeys[$k]);
                }
            }

            if (!empty($diff->removedTables)) {
                $diff->removedTables = array_filter($diff->removedTables, function (Table $table) {
                    return !in_array($table->getName(), $this->disabledTables)
                        && !in_array($table->getName(), $this->enabledTables);
                });
            }

            // Do not drop foreign keys referencing disabled tables
            // (dropped foreign keys may result from records in orphanedForeignKeys or changedTables)

            $preservedForeignKeys = [];

            foreach ($diff->orphanedForeignKeys as $k => $key) {
                if (in_array($key->getForeignTableName(), $this->disabledTables)) {
                    unset($diff->orphanedForeignKeys[$k]);

                    $preservedForeignKeys[] = $key;
                }
            }

            foreach ($diff->changedTables as $changedTable) {
                foreach ($changedTable->removedForeignKeys as $k => $key) {
                    if (in_array($key->getForeignTableName(), $this->disabledTables)) {
                        unset($changedTable->removedForeignKeys[$k]);

                        $preservedForeignKeys[] = $key;
                    }
                }
            }

            if ($preservedForeignKeys) {
                foreach ($diff->changedTables as $changedTable) {
                    foreach ($changedTable->removedIndexes as $k => $index) {
                        $indexForAForeignKey = (bool) array_filter($preservedForeignKeys, static function ($fkey) use ($index) {
                            return $fkey->intersectsIndexColumns($index);
                        });

                        if ($indexForAForeignKey) {
                            unset($changedTable->removedIndexes[$k]);
                        }
                    }
                }
            }
        }

        // Do not drop disabled columns, change them to nullable:

        foreach ($this->disabledColumns as $table => $fields) {
            foreach ($fields as $columnName => $change) {
                if (
                    isset($diff->changedTables[strtolower($table)])
                    && isset($diff->changedTables[$table]->removedColumns[strtolower($columnName)])
                ) {
                    $changedTable  = $diff->changedTables[strtolower($table)];
                    $removedColumn = $changedTable->removedColumns[strtolower($columnName)];

                    unset($changedTable->removedColumns[strtolower($columnName)]);

                    if ($removedColumn->getNotnull() && $removedColumn->getDefault() === null) {
                        $default = $this->getColumnDefaultValue($removedColumn->getType());
                        if ($default !== null) {
                            $changedTable->changedColumns[strtolower($columnName)] =
                                new ColumnDiff($columnName, $removedColumn->setDefault($default), ['default']);
                        }
                    }
                }
            }
        }

        foreach ($diff->changedTables as $table) {
            $this->detectIndexRenamingsOverridden($table);
        }

        return $diff;
    }

    /**
     * Get default value for column depending on its type
     *
     * @param \Doctrine\DBAL\Types\Type $columnType
     *
     * @return null|string
     */
    protected function getColumnDefaultValue($columnType)
    {
        switch ($columnType->getBindingType()) {
            case \PDO::PARAM_INT:
            case \PDO::PARAM_BOOL:
                $result = 0;
                break;
            case \PDO::PARAM_STR:
                $result = '';
                break;
            case \PDO::PARAM_LOB:
            default:
                $result = null;
        }

        return $columnType->convertToDatabaseValue($result, $this->connection->getDatabasePlatform());
    }

    /**
     * Try to find indexes that only changed their name, rename operations maybe cheaper than add/drop
     * however ambiguities between different possibilities should not lead to renaming at all.
     *
     * @return void
     */
    private function detectIndexRenamingsOverridden(TableDiff $tableDifferences)
    {
        $renameCandidates = [];

        // Gather possible rename candidates by comparing each added and removed index based on semantics.
        foreach ($tableDifferences->addedIndexes as $addedIndexName => $addedIndex) {
            foreach ($tableDifferences->removedIndexes as $removedIndex) {
                if ($this->comparator->diffIndex($addedIndex, $removedIndex)) {
                    continue;
                }

                $renameCandidates[$addedIndex->getName()][] = [$removedIndex, $addedIndex, $addedIndexName];
            }
        }

        foreach ($renameCandidates as $candidateIndexes) {
            // If the current rename candidate contains exactly one semantically equal index,
            // we can safely rename it.
            // Otherwise it is unclear if a rename action is really intended,
            // therefore we let those ambiguous indexes be added/dropped.
            if (count($candidateIndexes) !== 1) {
                continue;
            }

            [$removedIndex, $addedIndex] = $candidateIndexes[0];

            $removedIndexName = strtolower($removedIndex->getName());
            $addedIndexName   = strtolower($addedIndex->getName());

            if (isset($tableDifferences->renamedIndexes[$removedIndexName])) {
                continue;
            }

            // See XCB-305 for details
            // $tableDifferences->renamedIndexes[$removedIndexName] = $addedIndex;
            unset(
                $tableDifferences->addedIndexes[$addedIndexName],
                $tableDifferences->removedIndexes[$removedIndexName]
            );
        }

        // See XCB-1792 for details
        foreach ($tableDifferences->renamedIndexes as $renameIndexName => $renameIndex) {
            if (
                strpos($renameIndexName, 'idx_') === 0
                || strpos($renameIndexName, 'uniq_') === 0
            ) {
                unset($tableDifferences->renamedIndexes[$renameIndexName]);
            }
        }
    }
}
