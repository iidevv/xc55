<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

use XLite\InjectLoggerTrait;

/**
 * Categories processor
 */
class Categories extends \XLite\Logic\Import\Processor\Categories
{
    use InjectLoggerTrait;

    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        $this->updateAutoincrementCategory();
    }

    protected function updateAutoincrementCategory()
    {
        $connection      = \XLite\Core\Database::getEM()->getConnection();
        $repo            = \XLite\Core\Database::getRepo('XLite\Model\Category');
        $translationRepo = \XLite\Core\Database::getRepo('XLite\Model\CategoryTranslation');
        $quickFlagsRepo  = \XLite\Core\Database::getRepo('XLite\Model\Category\QuickFlags');

        $rootCategoryId = $repo->getRootCategoryId();

        $prefix = self::getTablePrefix();

        $id = static::getTrueAutoIncrement("{$prefix}categories");

        // TODO rewrite also the AUTO_INCREMENT below
        $xcartIdStmt = $connection->executeQuery('SELECT AUTO_INCREMENT'
            . ' FROM INFORMATION_SCHEMA.TABLES'
            . ' WHERE TABLE_SCHEMA = DATABASE()'
            . " AND TABLE_NAME = '{$repo->getTableName()}'");
        if ($xcartIdStmt) {
            $xcartId = $xcartIdStmt->fetchOne();
            $xcartId = ($xcartId - 1) != $rootCategoryId ? $xcartId : $rootCategoryId;
            $id = $xcartId > $id ? $xcartId : $id;
        }

        $connection->exec('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeUpdate(
            'UPDATE ' . $repo->getTableName() . ' SET category_id = ? WHERE category_id = ?',
            [$id, $rootCategoryId]
        );
        $connection->executeUpdate(
            'UPDATE ' . $repo->getTableName() . ' SET parent_id = ? WHERE parent_id = ?',
            [$id, $rootCategoryId]
        );
        $connection->executeUpdate(
            'UPDATE ' . $translationRepo->getTableName() . ' SET id = ? WHERE id = ?',
            [$id, $rootCategoryId]
        );
        $connection->executeUpdate(
            'UPDATE ' . $quickFlagsRepo->getTableName() . ' SET category_id = ? WHERE category_id = ?',
            [$id, $rootCategoryId]
        );
        $connection->exec('SET FOREIGN_KEY_CHECKS = 1');
        $connection->exec('ALTER TABLE ' . $repo->getTableName() . ' AUTO_INCREMENT = ' . ($id + 1));

        \XLite\Core\Database::getInstance()->startEntityManager();

        // Update metadata to use custom ID value
        static::updateMetadata();

        $e = $repo->getRootCategory(true);
        $e->setCategoryId($id);
    }

    /**
     * Update entities metadata
     */
    public static function updateMetadata()
    {
        $metadata = \XLite\Core\Database::getEM()->getClassMetadata('XLite\Model\Category');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
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
     * @return array
     */
    public function defineDataRemovers()
    {
        return [
            'categories',
        ];
    }

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePreProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Languages',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        // Mark columns as trusted
        $columns['description'][self::COLUMN_IS_TRUSTED] = true;

        // Parse Description Images
        $columns['description'][self::COLUMN_PARSE_IMAGES] = 'parse_after_normalization';

        return $columns
            + [
                'metaDescType' => [],
                'xc4EntityId'  => [],
                'parentid_to_adjustpos'  => [],// Don't mess with the original field name
                'cleanURLType'  => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $languageFields  = static::getCategoryLanguageFieldsSQL();
        $cleanURLsFields = static::getCleanURLsFieldsSQLfor('cleanURL');
        $availableLanguages = Configuration::getAvailableLanguages();

        $titleTags = static::getLanguageFieldsSQLfor(
            [
                'c.title_tag'    => 'metaTitle',
            ],
            $availableLanguages
        );
        if (version_compare(static::getPlatformVersion(), '4.3.0') < 0) {
            $titleTags = '"" metaTitle,';
        }

        $metaDescription = static::getLanguageFieldsSQLfor(
            [
                'c.meta_description'    => 'metaDesc',
            ],
            $availableLanguages
        );
        $metaDescription .= 'IF(c.meta_description <> "", "C", "A") metaDescType,';
        if (version_compare(static::getPlatformVersion(), '4.2.0') < 0) {
            $metaDescription = static::getLanguageFieldsSQLfor(
                [
                    'c.meta_descr'    => 'metaDesc',
                ],
                $availableLanguages
            );
            $metaDescription .= 'IF(c.meta_descr <> "", "C", "A") metaDescType,';
        }

        return 'c.categoryid xc4EntityId,'
            . 'c.categoryid categoryId,'
            . $languageFields
            . 'c.parentid parentid_to_adjustpos,'
            . 'IF(c.parentid = 0, "root", c.parentid) path,'
            . $titleTags
            . 'c.meta_keywords metaTags,'
            . $metaDescription
            . 'c.categoryid memberships,'
            . $cleanURLsFields
            . 'IF(c.avail = "Y", TRUE, FALSE) enabled,'
            . 'TRUE showTitle,'
            . 'c.order_by position';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix          = static::getTablePrefix();
        $cleanURLsTables = static::getCleanURLsJoinSQLfor(Configuration::CLEAN_URL_TYPE_C, 'c.categoryid', 'LEFT');

        return "{$prefix}categories c "
            . $cleanURLsTables;
    }

    /**
     * Define Filter SQL To Work Static Cache Properly In AdjustPositionValue Function
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        return ['c.parentid'];
    }


    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            self::REGISTRY_SOURCE => 'categoryId',
            self::REGISTRY_RESULT => 'category_id',
        ];
    }

    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'memberships',
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
        return static::t('Categories migrated');
    }

    /**
     * Get Category language fields SQL
     *
     * @return string
     */
    public static function getCategoryLanguageFieldsSQL()
    {
        return static::getLanguageFieldsSQLfor(
            [
                'c.`category`'    => 'name',
                'c.`description`' => 'description',
            ],
            Configuration::getAvailableLanguages()
        );
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating categories');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Returns Category Path By Category Data (CategoryId, Name And Path Used)
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizePathValue($value)
    {
        $categoryId = $this->normalizeValueAsUinteger($value['categoryId']);
        $name       = $this->normalizeValueAsString($this->getDefLangValue($value['name']));
        $result     = [$categoryId => $name];

        $path = $this->normalizeValueAsUinteger($value['path']);
        if ($path > 0) {
            $PDOStatement = $this->getPathValuePDOStatement();
            $parentId     = $categoryId !== $path ? $path : false;
            while (
                !empty($parentId)
                && ((($cachePresent = static::hasMigrationCache('categoryPathValue', $parentId))
                        && $record = static::getMigrationCache('categoryPathValue', $parentId))
                    || ($PDOStatement
                        && $PDOStatement->execute([$parentId])
                        && $record = $PDOStatement->fetch(\PDO::FETCH_ASSOC)))
            ) {
                if (!$cachePresent) {
                    static::setMigrationCache('categoryPathValue', $parentId, $record);
                }

                // Unshift Path Array With Parent Category
                $result   = [$parentId => $this->getDefLangValue($record['category'])] + $result;
                $parentId = $record['parentid'];
            }
        }

        $rootCategoryId = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();

        return [$rootCategoryId => ''] + $result;
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getPathValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT parentid, category'
            . " FROM {$prefix}categories"
            . ' WHERE categoryid = ?'
        );
    }

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeNameValue($value)
    {
        return $this->getI18NValues($this->currentRowData['categoryId'], 'category', $value);
    }

    /**
     * Normalize 'description' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeDescriptionValue($value)
    {
        return $this->getI18NValues($this->currentRowData['categoryId'], 'description', $value);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getLngDataPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT code, category, description'
            . " FROM {$prefix}categories_lng"
            . ' WHERE categoryid = ?'
        );
    }

    /**
     * Normalize 'memberships' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeMembershipsValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $result = [];

            $categoryId   = $this->currentRowData['categoryId'];
            $PDOStatement = $this->getMembershipsValuePDOStatement();
            if ($value && $PDOStatement && $PDOStatement->execute([$categoryId])) {
                while ($columnValue = $PDOStatement->fetchColumn()) {
                    $result[] = $columnValue;
                }
            }

            return $result;
        }, ['normalizeMembershipsValue', $value]);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getMembershipsValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT m.`membership`'
            . " FROM {$prefix}memberships m"
            . " INNER JOIN {$prefix}category_memberships cm"
            . ' ON cm.`categoryid` = ?'
            . ' AND cm.`membershipid` = m.`membershipid`'
        );
    }

    /**
     * Normalize 'cleanURL' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeCleanURLValue($value)
    {
        return $this->getCleanUrl(Configuration::CLEAN_URL_TYPE_C, $value);
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Category
     */
    protected function detectModel(array $data)
    {
        $categoryId = $this->normalizeValueAsUinteger($data['categoryId']);

        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find($categoryId);
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Category
     */
    protected function createModel(array $data)
    {
        return $this->addCategoryByPath($this->normalizePathValue($data));
    }

    /**
     * Import 'categoryId' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     */
    protected function importCategoryIdColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $model->setCategoryId((int) $value);
    }

    /**
     * Import 'memberships' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param array                 $value  Value
     * @param array                 $column Column info
     */
    protected function importMembershipsColumn(\XLite\Model\Category $model, array $value, array $column)
    {
        parent::importMembershipsColumn($model, $this->normalizeMembershipsValue($value), $column);
    }

    /**
     * Import 'path' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function importPathColumn(\XLite\Model\Category $model, $value, array $column)
    {
    }

    protected function importCleanURLColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $value = $this->normalizeCleanURLValue($value);

        parent::importCleanURLColumn($model, $value, $column);
    }

    /**
     * Import 'Parentid_to_adjustpos' Value. To Avoid Dec 03 16:05:57 XLite [Error] Requested Setter For Unknown Property: XLite\Model\Category::Parentid_to_adjustpos
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function importParentidToAdjustposColumn(\XLite\Model\Category $model, $value, array $column)
    {
    }


    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        if (empty($data['name'])) {
            return false;
        }

        $data['position'] = $this->adjustPositionValue($data['position'], $data);

        $res = parent::importData($data);

        if ($this->currentlyProcessingModel && isset($data['cleanURL'])) {
            $this->importOriginCleanURL_n_History($this->currentlyProcessingModel, $this->normalizeCleanURLValue($data['cleanURL']), $data);
        }

        return $res;
    }

    /**
     * Normalize 'Position' Value Function Cannot Be Used Due To Error Warning: Declaration Of XC\MigrationWizard\Logic\Import\Processor\XCart\Categories::NormalizePositionValue($Value, $Data) Should Be Compatible With XLite\Logic\Import\Processor\Categories::NormalizePositionValue($Value)
     *
     * @param mixed $value Value
     * @param mixed $value Data
     *
     * @return string
    */
    protected function adjustPositionValue($value, $data)
    {
        static $cats_withempty_orderby = [];

        $parent_catid = $data['parentid_to_adjustpos'];

        if (!isset($cats_withempty_orderby[$parent_catid])) {
            $cats_withempty_orderby = [];//Clear Previous Cat

            $prefix = static::getTablePrefix();
            if (version_compare(static::getPlatformVersion(), '4.4.0') >= 0) {
                $PDOStatement = static::getPreparedPDOStatement(
                    'SELECT `categoryid`'
                        . " FROM {$prefix}categories"
                        . " WHERE `parentid` = ? AND order_by = 0 ORDER BY lpos"
                );
            } else {
                $PDOStatement = static::getPreparedPDOStatement(
                    'SELECT `categoryid`'
                        . " FROM {$prefix}categories"
                        . " WHERE `parentid` = ? AND order_by = 0 ORDER BY category"
                );
            }

            if ($PDOStatement && $PDOStatement->execute([$parent_catid])) {
                $cats_withempty_orderby[$parent_catid] = array_flip($PDOStatement->fetchAll(\PDO::FETCH_COLUMN)) ?: false;
            } else {
                $this->getLogger('migration_errors')->error('', [
                    'processor' => get_called_class(),
                    'error' => 'false for if ($PDOStatement && $PDOStatement->execute([$data[parentid_to_adjustpos]]))',
                    'data' => $data
                ]);
            }
        }

        if (is_array($cats_withempty_orderby[$parent_catid])) {
            if (!empty($value)) {
                $value += ceil(count($cats_withempty_orderby[$parent_catid]) / 10) * 10;
            } else {
                $value = $cats_withempty_orderby[$parent_catid][$data['categoryId']];
            }
        }

        return $value;
    }

    // }}} </editor-fold>
}
