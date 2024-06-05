<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Manufacturers module
 */
class ManufacturersValues extends \XC\MigrationWizard\Logic\Import\Processor\AProcessor
{
    protected $manufacturerAttribute;

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_IS_KEY => true,
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'attribute' => [
                static::COLUMN_IS_KEY => true,
            ],
            'manufacturerid' => [],
            'xc4EntityId' => [],
            'position' => [],
            'addToNew' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeOption');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $languageFields = static::getManufacturerLanguageFieldsSQL();
        return "mf.manufacturer AS `name`"
            . ", mf.manufacturerid"
            . ", mf.manufacturerid AS xc4EntityId"
            . ", mf.orderby AS `position`"
            . ", 0 as `addToNew`, "
            . $languageFields;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        return "{$prefix}manufacturers AS mf";
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
        return [];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">
    /**
     * Get Manufacturer Language Fields SQL. MultiLanguage Attribute Option
     *
     * @return string
     */
    public static function getManufacturerLanguageFieldsSQL()
    {
        return static::getLanguageFieldsSQLfor(
            [
                'mf.`manufacturer`'    => 'name',
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
        return static::t('Migrating manufacturers');
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
                . " INNER JOIN {$prefix}products p"
                . ' ON p.manufacturerid = m.manufacturerid'
                . ' AND m.avail = "Y" LIMIT 1'
            );
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'name' value. MultiLanguage Attribute Option.
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

    protected function importManufactureridColumn($model, $value, array $column)
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
        if (!isset($this->manufacturerAttribute)) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_PRODUCT_CLASS} = null;
            $cnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_NAME} = 'Manufacturers';
            $rep = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->search($cnd);
            $attributeGroup = reset($rep);

            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS}   = null;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_ATTRIBUTE_GROUP} = $attributeGroup;
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_NAME}            = 'Manufacturer';

            $rep = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd);
            $this->manufacturerAttribute = reset($rep);
        }

        $data['attribute'] = $this->manufacturerAttribute;
        $result = false;
        if ($this->manufacturerAttribute) {
            $result = parent::importData($data);
        }

        return $result;
    }

    /**
     * Import 'attribute' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model Attribute value object
     * @param mixed                                       $value  Value
     * @param array                                       $column Column info
     *
     * @return void
     */
    protected function importAttributeColumn($model, $value, array $column)
    {
        $model->setAttribute($value);
    }
    // }}} </editor-fold>
}
