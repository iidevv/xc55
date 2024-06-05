<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\UpdateInventory\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\UpdateInventory")
 */
class VariantsInventory extends \XC\UpdateInventory\Logic\Import\Processor\VariantsInventory
{
    /**
     * Import 'Qty' value
     *
     * @param object $model  Product
     * @param mixed  $value  Value
     * @param array  $column Column info
     *
     * @return void
     */
    protected function importQtyColumn($model, $value, array $column)
    {
        if ($model->getProduct()->isSkippedFromSync()) {
            parent::importQtyColumn($model, $value, $column);
        }
    }
}
