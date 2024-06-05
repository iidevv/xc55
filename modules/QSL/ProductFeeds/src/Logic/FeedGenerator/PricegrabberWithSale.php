<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use XCart\Extender\Mapping\Extender;
use QSL\ProductFeeds\Core\FeedItem;

/**
 * Class generating feed files for Google Shopping comparison shopping website.
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\Sale", "QSL\ProductFeeds"})
 */
class PricegrabberWithSale extends \QSL\ProductFeeds\Logic\FeedGenerator\Pricegrabber
{
    /**
     * Get value for the listPrice column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getListPriceColumnValue(array $column, FeedItem $item)
    {
        return $item->isProductOnSale()
            ? $item->getDisplayPriceBeforeSale()
            : parent::getListPriceColumnValue($column, $item);
    }
}
