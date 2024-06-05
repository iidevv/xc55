<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Product selections page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ProductSelections extends \XLite\View\ProductSelections
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['sale_discount_product_selections'];
    }

    /**
     * Defines the search panel view class
     *
     * @return string
     */
    protected function getSearchPanelView()
    {
        return '\CDev\Sale\View\SearchPanel\ProductSelections\Admin\Main';
    }

    /**
     * Returns widget inner items list class
     *
     * @return string
     */
    protected function getItemsListClass()
    {
        return 'CDev\Sale\View\ItemsList\Model\SaleDiscountProductSelection';
    }
}
