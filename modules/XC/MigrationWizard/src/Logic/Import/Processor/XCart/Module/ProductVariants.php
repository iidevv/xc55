<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Product Variants module
 */
class ProductVariants extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const PV_CLASS = '';
    public const PV_GROUP = '';
    public const PV_TYPE  = \XLite\Model\Attribute::TYPE_SELECT;

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['XC\ProductVariants'];
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * @return bool
     */
    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return Configuration::isModuleEnabled(Configuration::MODULE_PRODUCT_OPTIONS)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$prefix}products AS p"
                . " INNER JOIN {$prefix}classes AS cs"
                . ' ON cs.productid = p.productid'
                . ' AND cs.is_modifier = "" LIMIT 1'
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
        return static::t('Migrating product variants');
    }
}
