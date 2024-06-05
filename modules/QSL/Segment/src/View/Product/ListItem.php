<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list item widget
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Get product data for Segment (as JSON)
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return string
     */
    protected function getSegmentProductData(\XLite\Model\Product $product)
    {
        return json_encode($this->defineSegmentProductData($product));
    }

    /**
     * Define product data for Segment
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    protected function defineSegmentProductData(\XLite\Model\Product $product)
    {
        return [
            'id'       => $product->getProductId(),
            'sku'      => $product->getSku(),
            'name'     => $product->getName(),
            'price'    => $product->getDisplayPrice(),
            'category' => $product->getCategory()->getStringPath(),

        ];
    }
}
