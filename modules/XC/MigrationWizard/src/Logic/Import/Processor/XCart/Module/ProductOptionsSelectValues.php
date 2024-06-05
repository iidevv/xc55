<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Product Options module
 */
class ProductOptionsSelectValues extends \XLite\Logic\Import\Processor\AttributeValues\AttributeValueSelect
{
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
        $type = ProductOptions::PO_TYPE;

        return "co.optionid AS `xc4EntityId`"
            . ", p.productcode AS `productSKU`"
            . ", cs.class AS `name`"
            . ", '{$class}' AS `class`"
            . ", '{$group}' AS `group`"
            . ", '{$type}' AS `type`"
            . ", 'Yes' AS `owner`"
            . ", co.option_name AS `value`"
            . ", co.orderby AS `valuePosition`"
            . ", '' AS `default`"
            . ", IF(co.modifier_type = '$', co.price_modifier, CONCAT(co.price_modifier, '%')) AS `priceModifier`"
            . ", '' AS `weightModifier`"
            . ", '' AS `editable`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        $id_generator_place_holder = self::GENERATOR_PLACEHOLDER;

        return "{$tp}products AS p"
            . " INNER JOIN {$tp}classes AS cs"
                . " ON cs.`productid` = p.`productid`"
                    . " AND (cs.`is_modifier` = 'Y' OR cs.`is_modifier` = '')"
                . "{$id_generator_place_holder}"
            . " INNER JOIN {$tp}class_options AS co"
                . " ON co.`classid` = cs.`classid`";
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

    /**
     * Define filter SQL XC4-148874
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        return ['co.classid', 'co.orderby'];
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
}
