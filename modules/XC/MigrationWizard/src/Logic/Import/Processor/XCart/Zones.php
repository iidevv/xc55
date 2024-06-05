<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Zones processor
 */
class Zones extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        $this->updateAutoincrementZone();
    }

    protected function updateAutoincrementZone()
    {
        $connection = \XLite\Core\Database::getEM()->getConnection();
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Zone');

        $prefix = self::getTablePrefix();

        $id = static::getTrueAutoIncrement("{$prefix}zones");

        // TODO rewrite also the AUTO_INCREMENT below
        $xcartIdStmt = $connection->executeQuery('SELECT AUTO_INCREMENT'
            . ' FROM INFORMATION_SCHEMA.TABLES'
            . ' WHERE TABLE_SCHEMA = DATABASE()'
            . " AND TABLE_NAME = '{$repo->getTableName()}'");
        if ($xcartIdStmt) {
            $xcartId = $xcartIdStmt->fetchOne();
            if (!$xcartId) {
                $xcartIdStmt = $connection->executeQuery("SELECT MAX(zone_id) FROM {$repo->getTableName()}");
                $xcartId = $xcartIdStmt->fetchOne();
            }
            $id = max($xcartId, $id);
        }

        $defaultZone = $repo->findOneBy(['is_default' => 1]);
        $defaultZoneId = null;
        if ($defaultZone) {
            $defaultZoneId = $defaultZone->getZoneId();
        }

        if ($defaultZoneId) {
            $connection->exec('SET FOREIGN_KEY_CHECKS = 0');

            $connection->executeUpdate('UPDATE ' . $repo->getTableName() . ' SET zone_id = ? WHERE is_default = 1', [$id]);

            $referencesStmt = $connection->executeQuery(
                'SELECT TABLE_NAME, COLUMN_NAME'
                . ' FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE'
                . ' WHERE REFERENCED_TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = ?',
                [$repo->getTableName()]
            );
            $references = $referencesStmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($references as $reference) {
                $connection->executeUpdate(
                    'UPDATE ' . $reference['TABLE_NAME'] . ' SET ' . $reference['COLUMN_NAME'] . ' = ? WHERE ' . $reference['COLUMN_NAME'] . ' = ?',
                    [$id, $defaultZoneId]
                );
            }

            $connection->exec('ALTER TABLE ' . $repo->getTableName() . ' AUTO_INCREMENT = ' . ($id + 1));
            $connection->exec('SET FOREIGN_KEY_CHECKS = 1');

            \XLite\Core\Database::getInstance()->startEntityManager();

            static::updateMetadata();
        }
    }

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     */
    public function __construct(\XLite\Logic\Import\Importer $importer)
    {
        parent::__construct($importer);

        // Update metadata to use custom ID value
        static::updateMetadata();
    }

    /**
     * Update entities metadata
     */
    public static function updateMetadata()
    {
        $metadata = \XLite\Core\Database::getEM()->getClassMetadata('XLite\Model\Zone');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'zone_id'       => [],
            'zone_name'     => [
                static::COLUMN_IS_KEY => true,
                static::COLUMN_LENGTH => 255,
            ],
            'zone_elements' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Zone');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return 'z.zoneid AS `zone_id`,'
            . 'z.zoneid AS `zone_name`,'
            . 'z.zoneid AS `zone_elements`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}zones AS z";
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            static::REGISTRY_SOURCE => 'zone_name',
            static::REGISTRY_RESULT => 'zone_id',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Zones migrated');
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating zones');
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * @param \XLite\Model\Zone $zone   Zone
     * @param string            $value  Value
     * @param array             $column Column info
     *
     * @return void
     */
    protected function importZoneIdColumn(\XLite\Model\Zone $zone, $value, array $column)
    {
        $zone->setZoneId((int) $value);
    }

    /**
     * Import 'zone name' value
     *
     * @param \XLite\Model\Zone $zone   Zone
     * @param string            $value  Value
     * @param array             $column Column info
     *
     * @return void
     */
    protected function importZoneNameColumn(\XLite\Model\Zone $zone, $value, array $column)
    {
        if ($value && $this->verifyValueAsEmpty($value)) {
            return;
        }

        $PDOStatement = $this->getZoneNamePDOStatement();
        if ($PDOStatement && $PDOStatement->execute([$value])) {
            $zone->setZoneName($PDOStatement->fetchColumn());
        }
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getZoneNamePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT zone_name'
            . " FROM {$prefix}zones"
            . ' WHERE zoneid = ?'
        );
    }

    /**
     * Import 'zone elements' value
     *
     * @param \XLite\Model\Zone $zone   Zone
     * @param string            $value  Value
     * @param array             $column Column info
     *
     * @return void
     */
    protected function importZoneElementsColumn(\XLite\Model\Zone $zone, $value, array $column)
    {
        if ($value && $this->verifyValueAsEmpty($value)) {
            return;
        }

        // Remove all zone elements if exists
        if ($zone->hasZoneElements()) {
            foreach ($zone->getZoneElements() as $element) {
                \XLite\Core\Database::getEM()->remove($element);
            }

            $zone->getZoneElements()->clear();

            \XLite\Core\Database::getEM()->persist($zone);
            \XLite\Core\Database::getEM()->flush($zone);
        }

        $PDOStatement = $this->getZoneElementPDOStatement();

        if ($PDOStatement && $PDOStatement->execute([$value])) {
            foreach ($PDOStatement->fetchAll(\PDO::FETCH_ASSOC) as $record) {
                $zoneElement = new \XLite\Model\ZoneElement();

                $zoneElement->setElementValue($record['field']);
                $zoneElement->setElementType($record['field_type']);

                $zoneElement->setZone($zone);

                $zone->addZoneElements($zoneElement);
            }
        }
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getZoneElementPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT field, field_type'
            . " FROM {$prefix}zone_element"
            . ' WHERE zoneid = ?'
        );
    }

    // }}} </editor-fold>
}
