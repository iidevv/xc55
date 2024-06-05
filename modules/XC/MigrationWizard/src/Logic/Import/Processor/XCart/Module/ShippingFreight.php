<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * ShippingFreight Based On Free Shipping Module
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */
class ShippingFreight extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku'         => [
                static::COLUMN_IS_KEY => true,
                static::COLUMN_LENGTH => 32,
            ],
            'freightFixedFee' => [],

            'xc4EntityId' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "p.productid AS `xc4EntityId`"
            . ", p.productcode AS `sku`"
            . ", p.shipping_freight AS `freightFixedFee`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        return "{$tp}products AS p";
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['XC\FreeShipping'];
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
            . " FROM {$prefix}products"
            . ' WHERE shipping_freight > 0 LIMIT 1'
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
        return static::t('Migrating shipping freights');
    }
}
