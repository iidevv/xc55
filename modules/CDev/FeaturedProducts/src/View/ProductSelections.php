<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View;

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
     * This widget is displayed only on the featured product selector
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['f_product_selections'];
    }

    /**
     * Defines the search panel view class
     *
     * @return string
     */
    protected function getSearchPanelView()
    {
        return 'CDev\FeaturedProducts\View\SearchPanel\Main';
    }
}
