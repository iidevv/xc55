<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Category;

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
        return ['category_product_selections'];
    }

    /**
     * Defines the search panel view class
     *
     * @return string
     */
    protected function getSearchPanelView()
    {
        return 'XLite\View\SearchPanel\Category\AddProducts';
    }

    /**
     * Defines the items list view class
     *
     * @return string
     */
    protected function getItemsListClass()
    {
        return 'XLite\View\ItemsList\Category\AddProducts';
    }
}
