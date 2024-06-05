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
class Inventory extends \XC\UpdateInventory\Logic\Import\Processor\Inventory
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
        if ($model->isSkippedFromSync()) {
            parent::importQtyColumn($model, $value, $column);
        }
    }
}
