<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use QSL\Backorder\Model\Product as BackorderProduct;
use XC\ProductVariants\Model\ProductVariant;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * CloudSearch store-side API methods
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\Backorder", "XC\ProductVariants"})
 */
abstract class StoreApiBackorderProductVariants extends \QSL\CloudSearch\Core\StoreApi
{
    /**
     * Get product variant stock status
     *
     * @param Product        $product
     * @param ProductVariant $variant
     *
     * @return string
     */
    protected function getVariantStockStatus(Product $product, ProductVariant $variant)
    {
        /** @var BackorderProduct $product */
        if ($product->getIsAvailableForBackorder()) {
            return StoreApiBackorder::INV_BACKORDER;
        }

        return parent::getVariantStockStatus($product, $variant);
    }
}
