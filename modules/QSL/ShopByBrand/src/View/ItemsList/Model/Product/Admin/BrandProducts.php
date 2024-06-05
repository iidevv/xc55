<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Model\Product\Admin;

use QSL\ShopByBrand\Model\Brand;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Product;
use XLite\View\Pager\Admin\Model\Table;

/**
 * Products list for a brand.
 */
class BrandProducts extends \XLite\View\ItemsList\Model\Product\Admin\CategoryProducts
{
    /**
     * @return array|string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['brand_products']);
    }

    /**
     * Get create button label.
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New product';
    }

    /**
     * Get the URL which the create button leads to.
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return $this->buildURL(
            'product',
            '',
            [
                'brand_id' => $this->getBrandId(),
            ]
        );
    }

    /**
     * The text that will be printed in case the list is empty.
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('Add existing products from the catalog, or create a new product.');
    }

    /**
     * Get top actions list.
     *
     * @return array
     */
    protected function getTopActions()
    {
        return array_merge(
            array_filter(
                parent::getTopActions(),
                static function ($value) {
                    return (strpos($value, 'add_products') === false);
                }
            ),
            [ 'modules/QSL/ShopByBrand/brand_products/parts/add_products.twig' ]
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' products-brand-add';
    }

    /**
     * @return CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new CommonCell();

        $result->{\QSL\ShopByBrand\Model\Repo\Product::P_BRAND_ID} = $this->getBrandId();
        $result->{\XLite\Model\Repo\Product::P_ORDER_BY}           = ['bp.orderby', 'asc'];

        return $result;
    }

    /**
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams       = parent::getCommonParams();
        $this->commonParams['id'] = $this->getBrandId();

        return $this->commonParams;
    }

    /**
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'id' => $this->getBrandId(),
            ]
        );
    }

    /**
     * @return string
     */
    protected function getPagerClass()
    {
        return Table::class;
    }

    /**
     * @return bool
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible()
            && $this->getPager()->isVisible();
    }

    /**
     * Get current brand ID.
     *
     * @return int
     */
    protected function getBrandId()
    {
        return (int) \XLite\Core\Request::getInstance()->id;
    }

    /**
     * @return bool
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'brand_products';
    }

    /**
     * @return string
     */
    protected function getMovePositionWidgetClassName()
    {
        return 'QSL\ShopByBrand\View\FormField\Input\BrandProductsOrderby';
    }

    /**
     * @param Product $product
     *
     * @return int
     */
    protected function getPositionColumnValue(Product $product)
    {
        /** @var Brand|null $brand */
        $brand = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([
            'brand_id' => (int) \XLite\Core\Request::getInstance()->id
        ]);
        return $product->getBrandPosition($brand);
    }

    protected function getPanelClass()
    {
        return 'QSL\ShopByBrand\View\StickyPanel\ItemsList\BrandProducts';
    }
}
