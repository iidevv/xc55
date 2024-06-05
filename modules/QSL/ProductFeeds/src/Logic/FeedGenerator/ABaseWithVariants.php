<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use QSL\ProductFeeds\Core\FeedItem;
use XCart\Extender\Mapping\Extender;

/**
 * Class with methods for exporting basic product fields.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class ABaseWithVariants extends \QSL\ProductFeeds\Logic\FeedGenerator\ABase
{
    /**
     * Get value for the productUrl column.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getProductUrlColumnValue(array $column, FeedItem $item)
    {
        if ($productId = $item->getProduct()->getProductId()) {
            $params = ['product_id' => $productId];

            if ($variant = $item->getVariant()) {
                $attrValues = array_reduce(
                    $variant->getValues(),
                    static function ($carry, $item) {
                        $carry[$item->getAttribute()->getId()] = $item->getId();
                        return $carry;
                    },
                    []
                );

                $params['attribute_values'] = $attrValues;
            }

            return $this->buildFeedUrl('product', '', $params);
        }

        return null;
    }
}
