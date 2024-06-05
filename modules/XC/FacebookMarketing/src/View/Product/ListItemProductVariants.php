<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("XC\FacebookMarketing")
 * @Extender\Depend("XC\ProductVariants")
 */
class ListItemProductVariants extends \XLite\View\Product\ListItem
{
    /**
     * @return string
     */
    protected function getFacebookPixelProductSku()
    {
        $product = $this->getProduct();

        if ($product->hasVariants()) {
            $variant = $product->getDefaultVariant();

            return $variant->getSku() ?: $variant->getVariantId();
        }

        return $product->getSku();
    }
}
