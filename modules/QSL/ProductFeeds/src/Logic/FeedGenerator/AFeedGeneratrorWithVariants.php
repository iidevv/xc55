<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use XCart\Extender\Mapping\Extender;
use QSL\ProductFeeds\Core\FeedItem;

/**
 * Base for classes generating feed files. A generator must not alter the feed.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class AFeedGeneratrorWithVariants extends \QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror
{
    /**
     * Get feed items for a product.
     *
     * @param \XLite\Model\Product $product Product to export.
     *
     * @return array
     */
    protected function getFeedItems(\XLite\Model\Product $product)
    {
        $items = [];

        $variants = $product->getVariants();
        if (count($variants)) {
            foreach ($variants as $variant) {
                $item = new FeedItem($product);
                $item->setVariant($variant);
                $items[] = $item;
            }
        } else {
            $items = parent::getFeedItems($product);
        }

        return $items;
    }
}
