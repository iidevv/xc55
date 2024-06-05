<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Extra Fields module
 */
class ExtraFields extends \XLite\Logic\Import\Processor\Attributes
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const EF_TYPE  = \XLite\Model\Attribute::TYPE_TEXT;
    public const EF_GROUP = 'Extra fields';

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        return $columns
            + [
                'fieldId' => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $type  = self::EF_TYPE;
        $group = self::EF_GROUP;

        $languageFields = static::getExtraFieldsLanguageFieldsSQL();

        return 'ef.fieldid `fieldId`,'
            . 'ef.field `name`,'
            . 'ef.orderby `position`,'
            . $languageFields
            . 'NULL `product`,'
            . 'NULL `class`,'
            . "'{$group}' `group`,"
            . "'{$type}' `type`,"
            . 'null `options`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}extra_fields AS ef";
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get product language fields SQL
     *
     * @return string
     */
    public static function getExtraFieldsLanguageFieldsSQL()
    {
        return static::getLanguageFieldsSQLfor(
            [
                'ef.`field`' => 'name',
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
        return static::t('Migrating extra fields');
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
        return $this->getI18NValues($this->currentRowData['fieldId'], 'field', $value);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getLngDataPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT code, field'
            . " FROM {$prefix}extra_fields_lng"
            . ' WHERE fieldid = ?'
        );
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}extra_fields"
            . ' WHERE active = "Y" LIMIT 1'
        );
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'productId' value
     *
     * @param \XLite\Model\Attribute $model  Product
     * @param string                 $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importFieldIdColumn(\XLite\Model\Attribute $model, $value, array $column)
    {
    }

    /**
     * Import 'group' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param string                 $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importGroupColumn(\XLite\Model\Attribute $model, $value, array $column)
    {
        if ($value) {
            $group = $this->normalizeGroupValue($value);
            $group->setProductClass($model->getProductClass());
            $model->setAttributeGroup($group);
        }
    }

    // }}} </editor-fold>
}
