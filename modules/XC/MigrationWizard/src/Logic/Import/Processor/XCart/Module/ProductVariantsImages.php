<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Product Variants images processor
 */
class ProductVariantsImages extends \XLite\Logic\Import\Processor\Products
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'xc4EntityId'    => [
                static::COLUMN_IS_MULTIPLE  => true,
            ],
        ];

        $columns += parent::defineColumns();

        return $columns;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "v.variantid AS `xc4EntityId`"
            . ", p.productcode AS `sku`"
            . ", v.productcode AS `variantSKU`"
            . ", v.variantid AS `variantID`"
            . ", v.variantid AS `variantImage`"
            . ", '' AS `variantImageAlt`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        $id_generator_place_holder = self::GENERATOR_PLACEHOLDER;

        return "{$prefix}variants AS v"
            . " INNER JOIN {$prefix}products AS p"
            . ' ON p.`productid` = v.`productid`'
            . ' AND v.`variantid` <> 0'
            . $id_generator_place_holder;
    }

    /**
     * Define filter SQL
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        return ['v.productid', 'v.variantid'];
    }

    /**
     * Define ID generator data
     *
     * @return array
     */
    public static function defineIdGenerator()
    {
        $prefix = self::getTablePrefix();

        return [
            'table' => "{$prefix}variants",
            'alias' => 'v',
            'order' => ['v.productid', 'v.variantid'],
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

    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'variantImage',
        ];
    }


    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product variants images');
    }

    public static function getImagesCount()
    {
        $prefix = self::getTablePrefix();

        $where = '1';
        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $subSelect = "SELECT v.variantid FROM {$prefix}variants AS v"
                . " INNER JOIN {$prefix}products AS p"
                . ' ON p.`productid` = v.`productid`'
                . ' AND v.`variantid` <> 0'
                . " WHERE p.productid IN ({$productIds})";

                $where = "i.id IN ({$subSelect})";
            }
        }

        $imagesCount = static::getCellData(
            "SELECT COUNT(i.id) FROM {$prefix}images_W as i WHERE {$where}"
        );

        return $imagesCount;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'variant image' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeVariantImageValue($value)
    {
        $res = is_array($value) ? $value : [$value];

        foreach ($res as $k => $v) {
            $res[$k] = $this->executeCachedRuntime(function () use ($v) {
                return $this->getImageURL('W', $v);
            }, ['NormalizeVariantImageValue', $v]);
        }

        return is_array($value) ? $res : array_pop($res);
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'variantImage' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantImageColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (!$this->verifyValueAsNull($value)) {
            foreach ($value as $key => $id) {
                $value[$key] = $this->normalizeVariantImageValue($id);
            }
        }

        parent::importVariantImageColumn($model, $value, $column);
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
        $key = static::VARIANT_PREFIX . 'ID';
        if (isset($data[$key])) {
            foreach ($data[$key] as $k => $v) {
                $data[$key][$k] = strlen($v) > 32 ? md5($v) : $v;
            }
        }

        return parent::importData($data);
    }

    // }}} </editor-fold>

    // {{{ Verification helpers

    protected function verifyValueAsFile($value)
    {
        $res = parent::verifyValueAsFile($value);

        // For Some Reason Parent VerifyValueAsFile Can Deal With Images Within Xc5 Root Dir Only
        if (
            $res === false
            && \Includes\Utils\FileManager::isReadable(LC_DIR_ROOT . $value)
        ) {
            $res = true;
        }

        return $res;
    }

    // }}}

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
     * Return true if import run in update-only mode
     *
     * @return boolean
     */
    protected function isUpdateMode()
    {
        return true;
    }
}
