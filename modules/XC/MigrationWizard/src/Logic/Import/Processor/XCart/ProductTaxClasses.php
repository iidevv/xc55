<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Tax Classes For Products
 */
class ProductTaxClasses extends \XLite\Logic\Import\Processor\Products
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    public const FREE_TAX_NAME = 'Tax free class';

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
        return 'pt.productid AS `xc4EntityId`,'
            . 'pt.productcode `sku`,'
            . 'pt.taxid AS `taxClass`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "(SELECT productid,productcode, NULL AS 'taxid' FROM {$prefix}products WHERE free_tax='Y' UNION DISTINCT SELECT productid,productcode,taxid FROM {$prefix}products p LEFT JOIN {$prefix}product_taxes pt USING(productid) WHERE pt.taxid IS NULL) AS pt";
    }


    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    protected function normalizeTaxClassValue($value)
    {
        if (empty($value)) {
            // Set Zero Free Class
            $value = static::FREE_TAX_NAME;
        } else {
            /*
            Currently Disabled As Many Tax Modules Have Own Tax Classes
            $value = $this->executeCachedRuntime(function () use ($value) {
                $prefix = self::getTablePrefix();

                $alt_lng_pdo_statement = static::getPreparedPDOStatement('SELECT value' . " FROM {$prefix}languages_alt" . ' WHERE name = ? AND code = ?');
                $lng = Configuration::getDefaultCustomerLanguage();

                if ($alt_lng_pdo_statement
                    && $alt_lng_pdo_statement->execute([$value, $lng])
                    && ($found = $alt_lng_pdo_statement->fetch(\PDO::FETCH_COLUMN))
                ) {
                    return $found;
                } else {
                    return static::getCellData("SELECT tax_name FROM {$prefix}taxes WHERE taxid=" . intval($value));
                }

            }, ['NormalizeTaxClassValue', $value]);

            $value = empty($value) ? $value : ($value . ' tax class');
            */
        }

        return parent::normalizeTaxClassValue($value);
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();
        /*
        Currently Disabled As Many Tax Modules Have Own Tax Classes
        $count_uniq_classes = ((int) static::getCellData("SELECT COUNT(DISTINCT taxid) FROM {$prefix}product_taxes"));

        if ($count_uniq_classes > 1) {
            return true; // Has More Than One Taxes
        } elseif ($count_uniq_classes == 0) {
            return false; // No Assigned Tax For Products
        }
        */

        // If Only 1 Tax Assigned Than Search For Free Products
        $has_free_products = static::getCellData("SELECT COUNT(*) FROM {$prefix}taxes t INNER JOIN {$prefix}tax_rates tr USING(taxid) WHERE t.active='Y'") > 0
            && (
                (bool) static::getCellData("SELECT 1 FROM {$prefix}products LEFT JOIN {$prefix}product_taxes USING(productid) WHERE taxid IS NULL LIMIT 1")
                || (bool) static::getCellData("SELECT 1 FROM {$prefix}products WHERE free_tax='Y' LIMIT 1")
            );

        return $has_free_products;
    }

    // }}} </editor-fold>

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product tax classes');
    }

    /**
     * Return true if import run in update-only mode
     *
     * @return boolean
     */
    protected function isUpdateMode()
    {
        return true;
    }
}
