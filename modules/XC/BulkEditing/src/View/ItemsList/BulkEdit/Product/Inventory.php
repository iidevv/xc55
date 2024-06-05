<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\View\ItemsList\BulkEdit\Product;

/**
 * Abstract product list
 */
class Inventory extends \XC\BulkEditing\View\ItemsList\BulkEdit\AProduct
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_inventory';

        parent::__construct($params);
    }
}
