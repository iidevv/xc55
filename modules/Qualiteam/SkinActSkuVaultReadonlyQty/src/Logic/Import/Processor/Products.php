<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 */
class Products extends \XLite\Logic\Import\Processor\Products
{
    protected function importStockLevelColumn(Product $model, $value, array $column)
    {
        if ($model->isSkippedFromSync()) {
            parent::importStockLevelColumn($model, $value, $column);
        }
    }
}
