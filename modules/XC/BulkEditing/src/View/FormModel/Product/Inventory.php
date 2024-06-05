<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\View\FormModel\Product;

class Inventory extends AProduct
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_inventory';

        parent::__construct($params);
    }
}
