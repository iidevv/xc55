<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestFeed\Logic\Import\Processor;

use QSL\ProductFeeds\Model\GoogleShoppingCategory;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products
{
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'PRODUCT-FEEDS-PINTEREST-CAT'   => 'Cannot find a Pinterest category for the specified category name',
            ];
    }

    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['pinterestCategory'] = [];

        return $columns;
    }

    /**
     * Verify the 'google_shopping_category' column.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPinterestCategory($value, array $column)
    {
        $repo = Database::getRepo(GoogleShoppingCategory::class);

        if (!$this->verifyValueAsEmpty($value) && !$repo->findOneByName($value)) {
            $this->addWarning('PRODUCT-FEEDS-PINTEREST-CAT', ['column' => $column, 'value' => $value]);
        }
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
    protected function importPinterestCategoryColumn(Product $model, $value, array $column)
    {
        $model->setPinterestCat($this->normalizeValueAsString($value));
    }
}