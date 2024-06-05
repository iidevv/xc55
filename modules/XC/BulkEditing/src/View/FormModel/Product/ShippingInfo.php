<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\View\FormModel\Product;

class ShippingInfo extends AProduct
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_shipping_info';

        parent::__construct($params);
    }
}
