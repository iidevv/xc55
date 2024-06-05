<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\ProductVariants\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class Products extends \XLite\Logic\Import\Processor\Products
{
    /**
     * Import 'variantQuantity' value
     *
     * @param Product $model Product
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function importVariantQuantityColumn(Product $model, $value, array $column)
    {
        if ($model->isSkippedFromSync()) {
            parent::importVariantQuantityColumn($model, $value, $column);
        }
    }
}
