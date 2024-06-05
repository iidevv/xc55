<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Cart;

/**
 * Product list item widget
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        /** @var ProductVariantsStockAvailabilityPolicy $policy */
        $policy = $this->getParam(self::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY);
        $cart   = Cart::getInstance();

        $list[] = $policy->getFirstAvailableVariantId($cart);

        return $list;
    }

    /**
     * @return string
     */
    protected function getProductSku()
    {
        $product = $this->getProduct();

        return $product->hasVariants() ? $product->getDefaultVariant()->getDisplaySku() : parent::getProductSku();
    }
}
