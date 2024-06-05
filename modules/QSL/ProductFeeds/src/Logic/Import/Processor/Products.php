<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * Products
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products
{
    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'PRODUCT-FEEDS-NEXTAG-ID'       => 'Cannot find a Nextag category for the specified category number',
                'PRODUCT-FEEDS-SHOPZILLA-ID'    => 'Cannot find a Shopzilla category for the specified category number',
                'PRODUCT-FEEDS-PRICEGRABBER-ID' => 'Cannot find a Pricegrabber category for the specified category number',
                'PRODUCT-FEEDS-EBAYCOMM-ID'     => 'Cannot find an eBay Commmerce category for the specified category number',
                'PRODUCT-FEEDS-GOOGLESHOP-ID'   => 'Cannot find a Google Shopping category for the specified category number',
                'PRODUCT-FEEDS-GOOGLESHOP-CAT'   => 'Cannot find a Google Shopping category for the specified category name',
            ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['nextagId'] = [];
        $columns['shopzillaId'] = [];
        $columns['pricegrabberId'] = [];
        $columns['ebayCommerceId'] = [];
        if (\XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_export_category_names) {
            $columns['googleShoppingCategory'] = [];
        } else {
            $columns['googleShoppingId'] = [];
        }

        return $columns;
    }

    /**
     * Verify the 'nextag_id' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyNextagId($value, array $column)
    {
        $repo = Database::getRepo('QSL\ProductFeeds\Model\NextagCategory');

        if (!$this->verifyValueAsEmpty($value) && !$repo->find($value)) {
            $this->addWarning('PRODUCT-FEEDS-NEXTAG-ID', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify the 'shopzilla_id' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyShopzillaId($value, array $column)
    {
        $repo = Database::getRepo('QSL\ProductFeeds\Model\ShopzillaCategory');

        if (!$this->verifyValueAsEmpty($value) && !$repo->find($value)) {
            $this->addWarning('PRODUCT-FEEDS-SHOPZILLA-ID', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify the 'pricegrabber_id' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPricegrabberId($value, array $column)
    {
        $repo = Database::getRepo('QSL\ProductFeeds\Model\PricegrabberCategory');

        if (!$this->verifyValueAsEmpty($value) && !$repo->find($value)) {
            $this->addWarning('PRODUCT-FEEDS-PRICEGRABBER-ID', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify the 'ebay_commerce_id' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyEbayCommerceId($value, array $column)
    {
        $repo = Database::getRepo('QSL\ProductFeeds\Model\EBayCommerceCategory');

        if (!$this->verifyValueAsEmpty($value) && !$repo->find($value)) {
            $this->addWarning('PRODUCT-FEEDS-EBAYCOMM-ID', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify the 'google_shopping_id' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyGoogleShoppingId($value, array $column)
    {
        $repo = Database::getRepo('QSL\ProductFeeds\Model\GoogleShoppingCategory');

        if (!$this->verifyValueAsEmpty($value) && !$repo->find($value)) {
            $this->addWarning('PRODUCT-FEEDS-GOOGLESHOP-ID', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify the 'google_shopping_category' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyGoogleShoppingCategory($value, array $column)
    {
        $repo = Database::getRepo('QSL\ProductFeeds\Model\GoogleShoppingCategory');

        if (!$this->verifyValueAsEmpty($value) && !$repo->findOneByName($value)) {
            $this->addWarning('PRODUCT-FEEDS-GOOGLESHOP-CAT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Import 'nextag_id' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importNextagIdColumn(Product $model, $value, array $column)
    {
        $model->setNextagId($this->normalizeValueAsString($value));
    }

    /**
     * Import 'shopzilla_id' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importShopzillaIdColumn(Product $model, $value, array $column)
    {
        $model->setShopzillaId($this->normalizeValueAsString($value));
    }

    /**
     * Import 'pricegrabber_id' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importPricegrabberIdColumn(Product $model, $value, array $column)
    {
        $model->setPricegrabberId($this->normalizeValueAsString($value));
    }

    /**
     * Import 'ebay_commerce_id' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importEbayCommerceIdColumn(Product $model, $value, array $column)
    {
        $model->setEbayId($this->normalizeValueAsString($value));
    }

    /**
     * Import 'google_shopping_id' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importGoogleShoppingIdColumn(Product $model, $value, array $column)
    {
        $model->setGoogleId($this->normalizeValueAsString($value));
    }

    /**
     * Import 'google_shopping_category' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importGoogleShoppingCategoryColumn(Product $model, $value, array $column)
    {
        $model->setGoogleCategory($this->normalizeValueAsString($value));
    }
}
