<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XLite\InjectLoggerTrait;
use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Product Options module
 */
class ProductOptionsTextValues extends \XLite\Logic\Import\Processor\AttributeValues\AttributeValueText
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
        return parent::defineColumns() + [
                'xc4EntityId' => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $class = ProductOptions::PO_CLASS;
        $group = ProductOptions::PO_GROUP;
        $type = \XLite\Model\Attribute::TYPE_TEXT;

        return "cs.classid AS `xc4EntityId`"
            . ", p.productcode AS `productSKU`"
            . ", cs.class AS `name`"
            . ", '{$class}' AS `class`"
            . ", '{$group}' AS `group`"
            . ", '{$type}' AS `type`"
            . ", 'Yes' AS `owner`"
            . ", '' AS `value`"
            . ", '' AS `default`"
            . ", '' AS `priceModifier`"
            . ", '' AS `weightModifier`"
            . ", 'Yes' AS `editable`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();
        $id_generator_place_holder = self::GENERATOR_PLACEHOLDER;

        return "{$prefix}products AS p"
            . " INNER JOIN {$prefix}classes AS cs"
                . ' ON cs.`productid` = p.`productid`'
                . $id_generator_place_holder;
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = "(cs.`is_modifier` = 'T' OR cs.`is_modifier` = 'A')";

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result .= " AND p.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    /**
     * Define ID generator data
     *
     * @return array
     */
    public static function defineIdGenerator()
    {
        $tp = self::getTablePrefix();

        return [
            'table' => "{$tp}products",
            'alias' => 'p',
            'order' => ['p.productid'],
        ];
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $dataset = self::defineDataset();

        return Configuration::isModuleEnabled(Configuration::MODULE_PRODUCT_OPTIONS)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$dataset} LIMIT 1"
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
        return static::t('Migrating product options');
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
