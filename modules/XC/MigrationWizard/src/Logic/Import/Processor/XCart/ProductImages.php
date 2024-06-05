<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Product images processor
 */
class ProductImages extends \XLite\Logic\Import\Processor\Products
{
    // {{{ Initialize <editor-fold desc="Initialize" defaultstate="collapsed">
    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        $this->importer->getOptions()->commonData['needRemoveDuplicateImages'] = true;
    }
    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        return $columns
            + [
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
        return 'p.productid `xc4EntityId`,'
            . 'p.productcode `sku`,'
            . 'p.productid `images`,'
            . '"" `imagesAlt`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}products AS p";
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
            'images',
        ];
    }

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariantsImages',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Product images migrated');
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product images');
    }

    public static function getImagesCount()
    {
        $prefix = self::getTablePrefix();

        $where = '1';
        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $where = "id IN ({$productIds})";
            }
        }

        $imagesCount = static::getCellData(
            "SELECT COUNT(*) FROM {$prefix}images_D WHERE {$where}"
        );

        $imagesCount += static::getCellData(
            "SELECT COUNT(*) FROM {$prefix}images_P WHERE {$where}"
        );

        $imagesCount += \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariantsImages::getImagesCount();

        return $imagesCount;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'images' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeImagesValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $productId = reset($value);
            $imagePath = $this->getImageURL('P', $productId, 'getalt');
            $result    = $imagePath ? ["P{$productId}" => $imagePath] : [];

            if (
                Configuration::isModuleEnabled(Configuration::MODULE_DETAILED_PRODUCT_IMAGES)
                && ($detailedImages = $this->getProductDetailedImageURL($productId))
            ) {
                $result += $detailedImages;
            }

            if (empty($result)) {
                $imagePath = $this->getImageURL('T', $productId, 'getalt');
                $result    = $imagePath ? ["T{$productId}" => $imagePath] : [];
            }

            return $result;
        }, ['NormalizeImagesValue', $value]);
    }

    /**
     * Return product detailed image URL
     *
     * @param integer $productId
     *
     * @return array
     */
    protected function getProductDetailedImageURL($productId)
    {
        $result = [];

        $prefix       = static::getTablePrefix();

        $PDOStatement = static::getPreparedPDOStatement(
            'SELECT imageid'
            . " FROM {$prefix}images_D"
            . ' WHERE id = ?'
            . ' ORDER BY orderby ASC'
        );

        if ($PDOStatement && $PDOStatement->execute([$productId])) {
            $images = $PDOStatement->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($images as $k => $imageId) {
                $result["D{$k}"] = $this->getImageURL('D', $imageId, 'getalt');
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'images' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importImagesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        $imgs = $this->normalizeImagesValue($value) ?: [];
        $imgs_flat = [];
        foreach ($imgs as $k => $image) {
            $imgs_flat[$k] = $image['path'];
        }
        $res = parent::importImagesColumn($model, $imgs_flat, $column);
        $this->importAlts($model, $imgs);
        return $res;
    }

    /**
     * Import 'images' Alt
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $_images
     *
     * @return void
     */
    protected function importAlts(\XLite\Model\Product $model, array $_images)
    {
        if (empty($_images) || !$model->getImages()) {
            return false;
        }

        foreach ($model->getImages() as $product_image) {
            $xc5_basename_img = '';
            foreach ($_images as $k => $image) {
                if (
                    $k[0] != 'D'// The First Symbol Of Index
                    || empty($image['alt'])
                ) {
                    // Currently Only D Images Are Supported
                    continue;
                }

                $xc4_basename_img = pathinfo(basename($image['path']));
                $xc4_basename_img = $xc4_basename_img['filename'];

                if (empty($xc4_basename_img)) {
                    continue;
                }

                $xc5_basename_img = $xc5_basename_img ? $xc5_basename_img : basename($product_image->getURL());

                if (
                    !empty($xc5_basename_img)
                    && stripos($xc5_basename_img, $xc4_basename_img) === 0
                ) {
                    // Update Alt Based On Image
                    $product_image->setAlt($image['alt']);
                }
            }
        }

        return true;
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
