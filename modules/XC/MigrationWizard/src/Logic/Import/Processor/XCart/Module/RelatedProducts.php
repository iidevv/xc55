<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Related Products module
 */
class RelatedProducts extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
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
            'product'       => [
                static::COLUMN_IS_KEY => true,
            ],
            'parentProduct' => [
                static::COLUMN_IS_KEY => true,
            ],
            'orderBy'       => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XC\Upselling\Model\UpsellingProduct');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "pl.productid1 AS `parentProduct`"
            . ", pl.productid2 AS `product`"
            . ", pl.orderby AS `orderBy`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        return "{$tp}product_links AS pl";
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
                $result = "pl.productid1 IN ({$productIds}) AND pl.productid2 IN ({$productIds})";
            }
        }

        return $result;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['XC\Upselling'];
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
     * Normalize 'parent product' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Product
     */
    protected function normalizeParentProductValue($value)
    {
        $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Product', $value);

        return $entry ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($entry->getResultId()) : null;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = self::getTablePrefix();

        return static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}product_links LIMIT 1"
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
        return static::t('Migrating related products');
    }
}
