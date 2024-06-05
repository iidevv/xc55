<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XCart\Extender\Mapping\ListChild;
use XLite\View\ProductSelections;

/**
 * Product selections page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PopupAddProductsSelector extends ProductSelections
{
    /**
     * Return list of allowed targets
     * This widget is displayed only on the featured product selector
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['brand_product_selections'];
    }

    /**
     * Defines the search panel view class
     *
     * @return string
     */
    protected function getSearchPanelView()
    {
        return 'QSL\ShopByBrand\View\SearchPanel\AddProducts';
    }

    /**
     * Defines the items list view class
     *
     * @return string
     */
    protected function getItemsListClass()
    {
        return 'QSL\ShopByBrand\View\ItemsList\Brand\AddProducts';
    }
}
