<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XLite\InjectLoggerTrait;
use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Manufacturers Module Meta_description Meta_keywords Descr Fields
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */
class ManufacturersValuesAdvancedFields extends \XC\MigrationWizard\Logic\Import\Processor\AProcessor
{
    use InjectLoggerTrait;

    protected $manufacturerAttribute;

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     *
     * /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersImages',
        ];
    }

    protected static function definePreProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Manufacturers',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersValues',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name'            => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'option'          => [
                static::COLUMN_IS_KEY => true,
            ],
            'manufacturerid'  => [],
            'xc4EntityId'     => [],
            'cleanURLType'    => [],
            'position'        => [],
            'cleanURLs'       => [],
            'description'     => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_IMPORT_EMPTY => true,//In Order To Transfer Data From Lng Table When The Mail Field Is Empty
                static::COLUMN_PARSE_IMAGES    => 'parse_after_normalization',
            ],
            'metaTitle'       => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'metaDescription' => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'metaKeywords'    => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
        ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $languageFields  = static::getManufacturerLanguageFieldsSQL();
        $cleanURLsFields = static::getCleanURLsFieldsSQLfor('cleanURLs');

        if (version_compare(static::getPlatformVersion(), '4.3.0') < 0) {
            $titleTags = ',"" AS `metaTitle`';
        } else {
            $titleTags = ', mf.title_tag AS `metaTitle`';
        }

        if (version_compare(static::getPlatformVersion(), '4.2.0') < 0) {
            $metaDescriptions = ',"" AS `metaDescription`';
        } else {
            $metaDescriptions = ", mf.meta_description as `metaDescription`";
        }

        if (version_compare(static::getPlatformVersion(), '4.2.0') < 0) {
            $_metaKeywords = ',"" AS `metaKeywords`';
        } else {
            $_metaKeywords = ", mf.meta_keywords as `metaKeywords`,";
        }

        return "mf.manufacturer AS `name`"
            . ", mf.manufacturerid"
            . ", mf.manufacturerid AS `xc4EntityId`"
            . ", mf.orderby AS `position`"
            . ", mf.descr as `description`"
            . $titleTags
            . $metaDescriptions
            . $_metaKeywords
            . $languageFields
            . $cleanURLsFields;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix          = static::getTablePrefix();
        $cleanURLsTables = static::getCleanURLsJoinSQLfor(Configuration::CLEAN_URL_TYPE_M, 'mf.manufacturerid', 'LEFT');

        return "{$prefix}manufacturers AS mf $cleanURLsTables";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        return $result;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['QSL\ShopByBrand'];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">
    /**
     * Get Manufacturer Language Fields SQL
     *
     * @return string
     */
    public static function getManufacturerLanguageFieldsSQL()
    {
        return static::getLanguageFieldsSQLfor(
            [
                'mf.`manufacturer`' => 'name',
                'mf.`descr`'        => 'description',
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
        return static::t('Migrating manufacturers fields');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand');
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return Configuration::isModuleEnabled(Configuration::MODULE_MANUFACTURERS)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$prefix}manufacturers m"
                . ' WHERE m.avail = "Y" LIMIT 1'
            );
    }

    /**
     * Assemble maodel conditions
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function assembleModelConditions(array $data)
    {
        return ($conditions = parent::assembleModelConditions($data));

        if (
            !empty($conditions['option'])
            && is_object($conditions['option'])
        ) {
            $conditions['option'] = $conditions['option']->getName();
        }

        return $conditions;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeNameValue($value)
    {
        return $this->getI18NValues($this->currentRowData['manufacturerid'], 'manufacturer', $value);
    }

    /**
     * Normalize 'Description' Value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeDescriptionValue($value)
    {
        return $this->getI18NValues($this->currentRowData['manufacturerid'], 'description', $value);
    }

    /**
     * Normalize 'cleanURL' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeCleanURLsValue($value)
    {
        return $this->getCleanUrl(Configuration::CLEAN_URL_TYPE_M, $value);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getLngDataPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT code, manufacturer, descr AS `description`'
            . " FROM {$prefix}manufacturers_lng"
            . ' WHERE manufacturerid = ?'
        );
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        if (empty($data['option']) || !$data['option']->id) {
            // The Option Has Been Just Created In Memory
            return null;
        }

        return parent::detectModel($data);
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
        static $already_inserted = [];

        if (!isset($this->manufacturerAttribute)) {
            $cnd                                                           = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_PRODUCT_CLASS} = null;
            $cnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_NAME}          = 'Manufacturers';

            $attributeGroup = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->search($cnd);
            $attributeGroup = reset($attributeGroup);

            $cnd                                                        = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT}         = null;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS}   = null;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_ATTRIBUTE_GROUP} = $attributeGroup;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_NAME}            = 'Manufacturer';

            $this->manufacturerAttribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd);
            $this->manufacturerAttribute = reset($this->manufacturerAttribute);
        }

        if (!$this->manufacturerAttribute) {
            $this->getLogger('migration_errors')->debug('', ['processor' => get_called_class(), 'error' => 'ManufacturerAttribute Not Found', 'data' => $data]);
            return parent::importData($data);
        }

        //Search Already Imported Option By Name
        $search_name_options = $this->getI18NValues($data['manufacturerid'], 'manufacturer', $data['name']) ?: [];
        foreach ($search_name_options as $_code => $_val) {
            if (strtolower($_code) == 'us') {
                $data['name']['en'] = $_val;
                unset($data['name']['us']);
            }

            $_option = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->findOneByNameAndAttribute($_val, $this->manufacturerAttribute);
            if ($_option) {
                break;
            }
        }

        if (
            empty($_option) && !empty($data['name'])
            && !isset($already_inserted[$data['manufacturerid']])
            && !$this->isUpdateMode()
        ) {
            // Create New Attribute Option
            $_option = new \XLite\Model\AttributeOption();
            $_option->setAttribute($this->manufacturerAttribute);
            $_option->setPosition($data['position']);
            \XLite\Core\Database::getEM()->persist($_option);
            $this->updateModelTranslations($_option, $data['name']);
            $already_inserted[$data['manufacturerid']] = true;
        } elseif (
            empty($_option)
            && !$this->isUpdateMode()
        ) {
            $this->getLogger('migration_errors')->debug('', ['processor' => get_called_class(), 'error' => 'ManufacturerOption Not Found', 'data' => $data]);
        }

        $data['option'] = $_option;

        $res = parent::importData($data);

        if (!$this->isUpdateMode() && $this->currentlyProcessingModel && isset($data['cleanURLs'])) {
            $this->importOriginCleanURL_n_History($this->currentlyProcessingModel, $this->normalizeCleanURLsValue($data['cleanURLs']), $data);
        }

        return $res;
    }

    /**
     * Import 'attribute' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model  Attribute value object
     * @param mixed                                       $value  Value
     * @param array                                       $column Column info
     *
     * @return void
     */
    protected function importOptionColumn($model, $value, array $column)
    {
        $model->setOption($value);
    }

    /**
     * Import 'Manufacturerid' Value
     *
     * @param XLite\Model\AttributeOption $model  Attribute option object
     * @param mixed                       $value  Value
     * @param array                       $column Column info
     *
     * @return void
     */
    protected function importManufactureridColumn($model, $value, array $column)
    {
    }

    protected function importNameColumn($model, $value, array $column)
    {
    }

    // }}} </editor-fold>
}
