<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Featured Products module
 */
class FeaturedProducts extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
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
            'product'  => [
                static::COLUMN_IS_KEY => true,
            ],
            'category' => [
                static::COLUMN_IS_KEY => true,
            ],
            'orderby'  => [],

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
        return \XLite\Core\Database::getRepo('CDev\FeaturedProducts\Model\FeaturedProduct');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "CONCAT_WS(',', productid, categoryid) as `xc4EntityId`"
            . ", productid AS `product`"
            . ", categoryid AS `category`"
            . ", product_order AS `orderby`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        return "{$tp}featured_products AS fp";
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['CDev\FeaturedProducts'];
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
                $result = "fp.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'product' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Product
     */
    protected function normalizeProductValue($value)
    {
        $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Product', $value);

        return $entry ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($entry->getResultId()) : null;
    }

    /**
     * Normalize 'category' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Category
     */
    protected function normalizeCategoryValue($value)
    {
        if ($value) {
            $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Category', $value);

            return $entry ? \XLite\Core\Database::getRepo('XLite\Model\Category')->find($entry->getResultId()) : null;
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Category')->findOneBy(['parent' => null]);
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}featured_products"
            . ' WHERE avail = "Y" LIMIT 1'
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
        return static::t('Migrating featured products');
    }
}
