<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Product Options module
 */
class ProductVariantsOptions extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductOptions
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $class = ProductVariants::PV_CLASS;
        $group = ProductVariants::PV_GROUP;
        $type  = ProductVariants::PV_TYPE;

        $options = static::getProductOptionsSQL();

        return "cs.classid AS `xc4EntityId`"
            . ", cs.class AS `name`"
            . ", cs.orderby AS `position`"
            . ", p.productcode AS `product`"
            . ", '{$class}' AS `class`"
            . ", '{$group}' AS `group`"
            . ", ( {$options} ) AS `options`"
            . ", '{$type}' AS `type`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = static::getTablePrefix();

        return "{$tp}products AS p"
            . " INNER JOIN {$tp}classes AS cs"
            . ' ON cs.`productid` = p.`productid`'
            . ' AND cs.`is_modifier` = ""';
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

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product variants');
    }
}
