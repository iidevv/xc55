<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XLite\InjectLoggerTrait;

/**
 * Extra Fields module
 */
class ExtraFieldsValues extends \XLite\Logic\Import\Processor\AttributeValues\AttributeValueText
{
    use InjectLoggerTrait;

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['xc4EntityId'] = [];

        return $columns;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $type  = ExtraFields::EF_TYPE;
        $group = ExtraFields::EF_GROUP;

        return "CONCAT_WS(',', efv.fieldid, efv.productid) AS `xc4EntityId`,"
            . 'p.productcode AS `productSKU`,'
            . 'ef.field AS `name`,'
            . 'null AS `class`,'
            . "'{$group}' AS `group`,"
            . "'{$type}' AS `type`,"
            . '"No" AS `owner`,'
            . 'efv.value AS `value`,'
            . '0 AS `default`,'
            . '"" AS `priceModifier`,'
            . '"" AS `weightModifier`,'
            . '"No" AS `editable`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix                 = static::getTablePrefix();
        $idGeneratorPlaceholder = self::GENERATOR_PLACEHOLDER;

        return "{$prefix}extra_field_values AS efv"
            . " INNER JOIN {$prefix}extra_fields AS ef"
            . ' ON ef.`fieldid` = efv.`fieldid`'
            . $idGeneratorPlaceholder
            . " INNER JOIN {$prefix}products AS p"
            . ' ON p.`productid` = efv.`productid`';
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
            'table' => "{$prefix}extra_field_values",
            'alias' => 'efv',
            'order' => ['efv.productid', 'efv.fieldid'],
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

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}extra_field_values LIMIT 1"
        );
    }

    // }}} </editor-fold>

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating extra fields values');
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    protected function createModel(array $data)
    {
        $product = $this->getProduct($data['productSKU']);

        $result = null;
        if ($product) {
            $result = parent::createModel($data);
        } else {
            $this->getLogger('migration_errors')->debug('', ['processor' => get_called_class(), 'error' => 'Product not found', 'data' => $data]);
        }

        return $result;
    }
}
