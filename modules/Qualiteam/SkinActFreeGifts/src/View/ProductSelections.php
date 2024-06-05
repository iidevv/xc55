<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View;

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
     * This widget is displayed only on the gift tier product selector
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['gift_tier_product_selections'];
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
