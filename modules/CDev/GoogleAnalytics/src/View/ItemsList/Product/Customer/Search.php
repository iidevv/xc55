<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;
use CDev\GoogleAnalytics\View\Product\ListItem;

/**
 * @Extender\Mixin
 *
 * Search
 */
class Search extends \XLite\View\ItemsList\Product\Customer\Search
{
    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param Product $product
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getProductWidgetParams(Product $product)
    {
        $result = parent::getProductWidgetParams($product);

        $result[ListItem::PARAM_LIST_READABLE_NAME] = 'SearchResults';

        return $result;
    }
}
