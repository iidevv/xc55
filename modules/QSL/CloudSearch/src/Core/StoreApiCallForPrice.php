<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * CloudSearch store-side API methods
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\CallForPrice"})
 */
abstract class StoreApiCallForPrice extends \QSL\CloudSearch\Core\StoreApi
{
    /**
     * Get single product data
     *
     * @param Product $product
     *
     * @return array
     */
    public function getProduct(Product $product)
    {
        $productData = parent::getProduct($product);

        if ($product->isCallForPrice()) {
            $productData['price'] = null;

            // If product has actual variants
            if (
                method_exists($product, 'getVariants')
                && $product->getVariants()->count() > 0
                && $product->getVariants()->exists(function ($key, $variant) {
                    return !$variant->isOutOfStock();
                }) > 0
            ) {
                $repo = Database::getRepo('\XC\ProductVariants\Model\ProductVariant');

                foreach ($productData['variants'] as $k => $variantData) {
                    $variant = $repo->find($variantData['id']);

                    if ($variant->isCallForPrice()) {
                        $productData['variants'][$k]['price'] = null;
                    }
                }

            } else {
                foreach ($productData['variants'] as $k => $variantData) {
                    $productData['variants'][$k]['price'] = null;
                }
            }
        }

        return $productData;
    }
}