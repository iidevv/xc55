<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
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
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getNextagIdColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getNextagCategory();

        return $c ? $c->getNextagId() : '';
    }

    /**
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getShopzillaIdColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getShopzillaCategory();

        return $c ? $c->getShopzillaId() : '';
    }

    /**
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPricegrabberIdColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getPricegrabberCategory();

        return $c ? $c->getPricegrabberId() : '';
    }

    /**
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEbayCommerceIdColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getEBayCommerceCategory();

        return $c ? $c->getEBayId() : '';
    }

    /**
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getGoogleShoppingIdColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getGoogleShoppingCategory();

        return $c ? $c->getGoogleId() : '';
    }

    /**
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getGoogleShoppingCategoryColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getGoogleShoppingCategory();

        return $c ? $c->getName() : '';
    }
}
