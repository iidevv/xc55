<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use XCart\Extender\Mapping\Extender;
use QSL\ProductFeeds\Core\FeedItem;

/**
 * Decorated class generating feed files for Google Shopping comparison shopping website.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\FreeShipping")
 */
class GoogleShoppingWithFreeShipping extends \QSL\ProductFeeds\Logic\FeedGenerator\GoogleShopping
{
    /**
     * Get value for the category column.
     *
     * @see https://support.google.com/merchants/answer/6324484?hl=en
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getShippingColumnValue(array $column, FeedItem $item)
    {
        $freight = 0 + $item->getFreightFixedFee();
        return ($item->getShippable() && ($item->getFreeShip() || ($freight > 0.0000001)))
            ? $this->getShippingColumnPrefix()
                . $this->formatFeedPrice($item->getFreeShip() ? 0 : $freight)
            : parent::getShippingColumnValue($column, $item);
    }
}
