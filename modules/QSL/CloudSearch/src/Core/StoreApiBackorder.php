<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use QSL\Backorder\Model\Product as BackorderProduct;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * CloudSearch store-side API methods
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\Backorder"})
 */
abstract class StoreApiBackorder extends \QSL\CloudSearch\Core\StoreApi
{
    const INV_BACKORDER = 'backorder';

    /**
     * Get product stock status
     *
     * @param Product $product
     *
     * @return string
     */
    protected function getProductStockStatus(Product $product)
    {
        /** @var BackorderProduct $product */
        if ($product->getIsAvailableForBackorder()) {
            return self::INV_BACKORDER;
        }

        return parent::getProductStockStatus($product);
    }

    protected function inStockConditions(): array
    {
        return array_merge(parent::inStockConditions(), [self::INV_BACKORDER]);
    }
}
