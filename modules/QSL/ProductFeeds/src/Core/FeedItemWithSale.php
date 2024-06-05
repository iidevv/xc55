<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated FeedItem class with Variants module enabled.
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class FeedItemWithSale extends \QSL\ProductFeeds\Core\FeedItem
{
    /**
     * Checks if the item has the sale price.
     *
     * @return boolean
     */
    public function isProductOnSale()
    {
        $product = $this->getProduct();
        $salePrice = $product->getDisplayPrice();
        $listPrice = $product->getDisplayPriceBeforeSale();

        return ($salePrice && (abs($listPrice - $salePrice) > 0.0000001)); // compare floats
    }
}
