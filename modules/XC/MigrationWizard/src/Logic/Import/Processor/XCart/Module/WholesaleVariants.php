<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Wholesale trading module
 */
class WholesaleVariants extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
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
            'productVariant' => [
                static::COLUMN_IS_KEY => true,
                static::COLUMN_LENGTH => 32,
            ],
            'quantityRangeBegin' => [
                static::COLUMN_IS_KEY => true,
            ],
            'quantityRangeEnd' => [],
            'price' => [],
            'membership' => [
                static::COLUMN_IS_KEY => true,
            ],

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
        return \XLite\Core\Database::getRepo('CDev\Wholesale\Model\ProductVariantWholesalePrice');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "pp.priceid AS `xc4EntityId`"
            . ", v.productcode AS `productVariant`"
            . ", pp.quantity AS `quantityRangeBegin`"
            . ", 0 AS `quantityRangeEnd`"
            . ", pp.price AS `price`"
            . ", m.membership AS `membership`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        return "{$tp}variants AS v"
            . " INNER JOIN {$tp}pricing AS pp"
                . " ON pp.`productid` = v.`productid`"
                . " AND pp.`quantity` > 1"
                . " AND pp.`variantid` <> 0"
                . " AND pp.`variantid` = v.`variantid`"
            . " LEFT JOIN {$tp}memberships AS m"
                . " ON m.`membershipid` = pp.`membershipid`";
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
            'table' => "{$tp}variants",
            'alias' => 'v',
            'order' => ['v.productid', 'pp.quantity'],
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
                $result = "v.productid IN ({$productIds})";
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
        return ['CDev\Wholesale', 'XC\ProductVariants'];
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * @return bool
     */
    protected static function checkTransferableDataPresent()
    {
        $dataset = self::defineDataset();

        return Configuration::isModuleEnabled(Configuration::MODULE_PRODUCT_OPTIONS)
            && Configuration::isModuleEnabled(Configuration::MODULE_WHOLESALE_TRADING)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$dataset} LIMIT 1"
            );
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize value as product variant
     *
     * @param mixed $value Value
     *
     * @return \CDev\Wholesale\Model\ProductVariant
     */
    protected function normalizeValueAsProductVariant($value)
    {
        $result = null;

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                ->findOneBy(['sku' => $value]);
        }

        return $result;
    }

    /**
     * Normalize 'product variant' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizeProductVariantValue($value)
    {
        return $this->normalizeValueAsProductVariant($value);
    }

    /**
     * Normalize 'membership' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizeMembershipValue($value)
    {
        return $this->normalizeValueAsMembership($value);
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'product variant' value
     *
     * @param \CDev\Wholesale\Model\ProductVariantWholesalePrice $wholesale  Wholesale price
     * @param string                                            $value  Value
     * @param array                                             $column Column info
     *
     * @return void
     */
    protected function importProductVariantColumn(\CDev\Wholesale\Model\ProductVariantWholesalePrice $wholesale, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && ($variant = $this->normalizeProductVariantValue($value))) {
            $this->wholesaleProductVariantModel = $variant;
            $wholesale->productVariant = $this->wholesaleProductVariantModel;
        }
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $result = parent::importData($data);

        if ($result && $this->wholesaleProductVariantModel) {
            // Additional correction to re-define end of subtotal range for each discount
            \XLite\Core\Database::getRepo('CDev\Wholesale\Model\ProductVariantWholesalePrice')
                ->correctQuantityRangeEnd($this->wholesaleProductVariantModel);
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
        return static::t('Migrating wholesales prices');
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        $conditions = $this->assembleModelConditions($data);

        return $conditions ? $this->getRepository()->findOneBy($conditions) : null;
    }

    /**
     * Assemble maodel conditions
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function assembleModelConditions(array $data)
    {
        $conditions = [];
        foreach ($this->getKeyColumns() as $column) {
            if (
                isset($data[$column[static::COLUMN_NAME]])
            ) {
                $conditions[$this->getModelPropertyName($column)] = $this->normalizeModelPlainProperty(
                    $data[$column[static::COLUMN_NAME]],
                    $column
                );
            }
        }

        return $conditions;
    }
}
