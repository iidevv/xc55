<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Products processor
 */
class Products extends \XLite\Logic\Import\Processor\Products
{
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
        $metadata = \XLite\Core\Database::getEM()->getClassMetadata('XLite\Model\Product');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        $metadata = \XLite\Core\Database::getEM()->getClassMetadata('XLite\Model\Category');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
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
//            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Bestsellers',// wait for MW-65
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Egoods',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ExtraFields',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeatureComparison',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Manufacturers',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersValues',
        ];
    }

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [
//            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Bestsellers',// wait for MW-65
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\EgoodsValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ExtraFieldsValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeatureComparisonCheckboxValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeatureComparisonSelectValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeatureComparisonTextValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeaturedProducts',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\MarketPrice',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ShippingFreight',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductOptions',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductOptionsSelectValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductOptionsTextValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariants',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariantsOptions',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariantsValues',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\RelatedProducts',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Wholesale',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\WholesaleVariants',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\ProductTaxClasses',
        ];
    }

    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'categories',
            'memberships',
        ];
    }

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
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        // Mark columns as trusted
        $columns['description'][self::COLUMN_IS_TRUSTED] = true;
        $columns['briefDescription'][self::COLUMN_IS_TRUSTED] = true;
        $columns['attributes'][self::COLUMN_IS_TRUSTED] = true;

        // Parse Description Images
        $columns['description'][self::COLUMN_PARSE_IMAGES] = 'parse_after_normalization';
        $columns['briefDescription'][self::COLUMN_PARSE_IMAGES] = 'parse_after_normalization';

        return $columns
            + [
                'productId' => [],
                'xc4EntityId' => [],
                'cleanURLType' => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $languageFields     = static::getProductLanguageFieldsSQL();
        $manufacturerFields = static::getProductManufacturerFieldsSQL();
        $cleanURLsFields    = static::getCleanURLsFieldsSQLfor('cleanURL');
        $availableLanguages = Configuration::getAvailableLanguages();

        $noInventoryTrackingOption = Configuration::getConfigurationOptionValue(
            Configuration::CONFIG_OPTION_NO_INVENTORY_TRACKING
        );

        $invTrackingValue = $noInventoryTrackingOption === Configuration::CONFIG_OPTION_VALUE_ENABLED
            ? 'false'
            : 'true';

        $safe_max_unixtime_limit = '2147000000'; // 2038-01-13 16:53:20
        $arrivalDateField = static::isTableColumnExists('products', 'show_as_new_from')
            ? "FROM_UNIXTIME(IF(p.show_as_new_from <> 0, p.show_as_new_from, LEAST(p.add_date, $safe_max_unixtime_limit)), GET_FORMAT(DATETIME,'ISO')) `arrivalDate`,"
            : "FROM_UNIXTIME(LEAST(p.add_date, $safe_max_unixtime_limit), GET_FORMAT(DATETIME,'ISO')) `arrivalDate`,";

        $titleTags = 'p.title_tag `metaTitle`,';
        if (version_compare(static::getPlatformVersion(), '4.3.0') < 0) {
            $titleTags = '"" `metaTitle`,';
        }

        $boxData = "IF(p.separate_box = 'Y', true, false ) `useSeparateBox`,"
            . 'p.width `boxWidth`,'
            . 'p.length `boxLength`,'
            . 'p.height `boxHeight`,'
            . 'p.items_per_box `itemsPerBox`,';

        $metaDescrFields = static::getLanguageFieldsSQLfor(
            [
                'p.meta_description' => 'metaDesc',
            ],
            $availableLanguages
        );
        $metaDescr = $metaDescrFields
            . 'p.meta_description `metaDescType`,';
        if (version_compare(static::getPlatformVersion(), '4.2.0') < 0) {
            $boxData = 'p.dim_y `boxWidth`,'
                . 'p.dim_x `boxLength`,'
                . 'p.dim_z `boxHeight`,';
            $metaDescr = '';
        }

        return 'p.productid `xc4EntityId`,'
            . 'p.productid `productId`,'
            . 'p.productcode `sku`,'
            . $languageFields
            . 'p.productid `categories`,'
            . 'IF(pr.price IS NULL, 0, pr.price) `price`,'
            . 'p.avail `stockLevel`,'
            . 'p.low_avail_limit `lowLimitLevel`,'
            . 'p.productid `memberships`,'
            . $arrivalDateField
            . "FROM_UNIXTIME(LEAST(p.add_date, $safe_max_unixtime_limit), GET_FORMAT(DATETIME,'ISO')) `date`,"
            . "FROM_UNIXTIME(LEAST(p.add_date, $safe_max_unixtime_limit), GET_FORMAT(DATETIME,'ISO')) `updateDate`,"
            . $boxData
            . 'p.weight `weight`,'
            . $titleTags
            . $metaDescr
            . "{$invTrackingValue} `inventoryTrackingEnabled`,"
            . "IF(p.forsale = 'Y', true, false ) `enabled`,"
            . "IF(p.free_shipping = 'Y', false, true) 'shippable',"
            . $manufacturerFields
            . $cleanURLsFields;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        $languageTables     = static::getProductLanguageJoinTablesSQL();
        $manufacturerTables = static::getProductManufacturerJoinTableSQL();
        $cleanURLsTables    = static::getCleanURLsJoinSQLfor(Configuration::CLEAN_URL_TYPE_P, 'p.productid', 'LEFT');

        return "{$prefix}products AS p"
            . " {$languageTables}"
            . " {$cleanURLsTables}"
            . " LEFT JOIN {$prefix}pricing AS pr"
            . ' ON pr.`productid` = p.`productid`'
            . " AND pr.`quantity` = '1'"
            . " AND pr.`variantid` = '0'"
            . " AND pr.`membershipid` = '0'"
            . " {$manufacturerTables}";
    }

    /**
     * Define ID generator data
     *
     * @return array
     */
    public static function defineIdGenerator()
    {
        $prefix = static::getTablePrefix();

        return [
            'table' => "{$prefix}products",
            'alias' => 'p',
            'order' => ['p.productid'],
        ];
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            self::REGISTRY_SOURCE => 'productId',
            self::REGISTRY_RESULT => 'product_id',
        ];
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result = "p.productid IN ({$productIds})";
            }
        }

        return $result;
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
        return static::t('Products migrated');
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating products');
    }

    /**
     * Get product language fields SQL
     *
     * @return string
     */
    public static function getProductLanguageFieldsSQL()
    {
        $fields    = '';
        $languages = Configuration::getAvailableLanguages();

        if (version_compare(static::getPlatformVersion(), '4.5.0') < 0) {
            $fields = static::getLanguageFieldsSQLfor(
                [
                    'p.`product`'   => 'name',
                    'p.`descr`'     => 'briefDescription',
                    'p.`fulldescr`' => 'description',
                    'p.`keywords`'  => 'metaTags',
                ],
                $languages
            );
        } else {
            $prefix = static::getTablePrefix();

            foreach ($languages as $lng) {
                $fields .= "{$prefix}products_lng_{$lng}.`product` `name_{$lng}`,"
                    . "{$prefix}products_lng_{$lng}.`descr` `briefDescription_{$lng}`,"
                    . "{$prefix}products_lng_{$lng}.`fulldescr` `description_{$lng}`,"
                    . "{$prefix}products_lng_{$lng}.`keywords` `metaTags_{$lng}`,";
            }
        }

        return $fields;
    }

    /**
     * Get product language join tables SQL
     *
     * @return string
     */
    public static function getProductLanguageJoinTablesSQL()
    {
        $join = '';

        if (version_compare(static::getPlatformVersion(), '4.5.0') < 0) {
            // No actions required
        } else {
            $languages = Configuration::getAvailableLanguages();
            $prefix    = static::getTablePrefix();

            $defaultCustomerLanguage = Configuration::getDefaultCustomerLanguage();
            $idGeneratorPlaceholder  = self::GENERATOR_PLACEHOLDER;

            $mkey = $dkey = 'productid';

            foreach ($languages as $lng) {
                $joinType = $lng === $defaultCustomerLanguage ? 'INNER' : 'LEFT';

                $join .= " {$joinType} JOIN {$prefix}products_lng_{$lng}"
                    . " ON {$prefix}products_lng_{$lng}.`{$dkey}` = p.`{$mkey}`"
                    . "{$idGeneratorPlaceholder}";
            }
        }

        return $join;
    }

    /**
     * Get product manufacturers fields SQL
     *
     * @return string
     */
    public static function getProductManufacturerFieldsSQL()
    {
        return Configuration::isModuleEnabled(Configuration::MODULE_MANUFACTURERS)
            ? 'm.manufacturer AS `Manufacturer (field:global >>> Manufacturers)`,'
            : '';
    }

    /**
     * Get product manufacturer join tables SQL
     *
     * @return string
     */
    public static function getProductManufacturerJoinTableSQL()
    {
        if (!Configuration::isModuleEnabled(Configuration::MODULE_MANUFACTURERS)) {
            return '';
        }

        $prefix                 = static::getTablePrefix();
        $idGeneratorPlaceholder = static::GENERATOR_PLACEHOLDER;

        return "LEFT JOIN {$prefix}manufacturers AS m"
            . ' ON m.`manufacturerid` = p.`manufacturerid`'
            . "{$idGeneratorPlaceholder}";
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'categories' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeCategoriesValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $result       = [];
            $productId    = reset($value);
            $PDOStatement = $this->getCategoriesPDOStatement();
            $categories   = [];

            if ($PDOStatement && $PDOStatement->execute([$productId])) {
                $categories = $PDOStatement->fetchAll(\PDO::FETCH_COLUMN);
            }

            foreach ($categories as $categoryId) {
                if ($path = $this->getCategoryPath($categoryId)) {
                    $result[] = $path;
                }
            }

            return $result;
        }, ['normalizeCategoriesValue', $value]);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getCategoriesPDOStatement()
    {
        /** @var string $prefix */
        $prefix = self::getTablePrefix();

        return self::getPreparedPDOStatement(
            'SELECT categoryid'
            . " FROM {$prefix}products_categories"
            . ' WHERE productid = ?'
            . ' ORDER BY FIELD(main, "Y", "N")'
        );
    }

    /**
     * @param integer $categoryId
     *
     * @return array
     */
    protected function getCategoryPath($categoryId)
    {
        $result       = [];
        $PDOStatement = $this->getPathValuePDOStatement();

        while (
            !empty($categoryId)
            && ((($cachePresent = static::hasMigrationCache('categoryPathValue', $categoryId))
                    && $record = static::getMigrationCache('categoryPathValue', $categoryId))
                || ($PDOStatement
                    && $PDOStatement->execute([$categoryId])
                    && $record = $PDOStatement->fetch(\PDO::FETCH_ASSOC)))
        ) {
            if (!$cachePresent) {
                static::setMigrationCache('categoryPathValue', $categoryId, $record);
            }

            // unshift path array with parent category
            $result     = [$categoryId => $this->getDefLangValue($record['category'])] + $result;
            $categoryId = $record['parentid'];
        }

        $rootCategoryId = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();

        return !empty($result) ? [$rootCategoryId => ''] + $result : false;
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
        if (version_compare(static::getPlatformVersion(), '4.5.0') >= 0) {
            return $value;
        }

        return $this->getI18NValues($this->currentRowData['productId'], 'product', $value);
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
        if (version_compare(static::getPlatformVersion(), '4.5.0') >= 0) {
            return $value;
        }

        return $this->getI18NValues($this->currentRowData['productId'], 'fulldescr', $value);
    }

    /**
     * Normalize 'brief description' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeBriefDescriptionValue($value)
    {
        if (version_compare(static::getPlatformVersion(), '4.5.0') >= 0) {
            return $value;
        }

        return $this->getI18NValues($this->currentRowData['productId'], 'descr', $value);
    }

    /**
     * Normalize 'meta tags' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeMetaTagsValue($value)
    {
        if (version_compare(static::getPlatformVersion(), '4.5.0') >= 0) {
            return $value;
        }

        return $this->getI18NValues($this->currentRowData['productId'], 'keywords', $value);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getLngDataPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT code, descr, fulldescr, keywords, product'
            . " FROM {$prefix}products_lng"
            . ' WHERE productid = ?'
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

            $categoryId   = $this->currentRowData['productId'];
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
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeMetaDescTypeValue($value)
    {
        return $value ? 'C' : 'A';
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
            . " FROM {$prefix}memberships AS m"
            . " INNER JOIN {$prefix}product_memberships AS pm"
            . ' ON pm.`productid` = ?'
            . ' AND pm.`membershipid` = m.`membershipid`'
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
        return $this->getCleanUrl(Configuration::CLEAN_URL_TYPE_P, $value);
    }

    /**
     * Normalize value as date
     *
     * TODO: remove if fixed in core ->https://xcart.slack.com/archives/xc5devsupport/p1473679497000028
     *
     * @param mixed $value Value
     *
     * @return integer
     */
    protected function normalizeValueAsDate($value)
    {
        $normalized = strtotime($value);

        return $normalized === false || $normalized < 0 ? time() : $normalized;
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'productId' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importProductIdColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setProductId((int) $value);
    }

    /**
     * Import 'categories' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importCategoriesColumn(\XLite\Model\Product $model, $value, array $column)
    {
        parent::importCategoriesColumn($model, $this->normalizeCategoriesValue($value), $column);
    }

    /**
     * Import 'memberships' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importMembershipsColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        parent::importMembershipsColumn($model, $this->normalizeMembershipsValue($value), $column);
    }

    /**
     * Import 'cleanURL' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importCleanURLColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $value = $this->normalizeCleanURLValue($value);

        parent::importCleanURLColumn($model, $value, $column);
    }

    // }}} </editor-fold>

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        $model = $this->getRepository()->find($data['productId']);

        return $model;
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

        $res = parent::importData($data);

        if ($this->currentlyProcessingModel && isset($data['cleanURL'])) {
            $this->importOriginCleanURL_n_History($this->currentlyProcessingModel, $this->normalizeCleanURLValue($data['cleanURL']), $data);
        }

        return $res;
    }

    /**
     * Update model translations
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $value Value
     * @param string               $name  Name OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    protected function updateModelTranslations(\XLite\Model\AEntity $model, array $value, $name = 'name')
    {
        if ($name === 'name') {
            foreach ($value as $code => $val) {
                if ($val == '') {
                    unset($value[$code]);
                }
            }
        }

        return parent::updateModelTranslations($model, $value, $name);
    }
}
