<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Model\DTO\Product;

class PriceAndMembership extends AProduct
{
    public function __construct($data = null)
    {
        $this->scenario = 'product_price_and_membership';

        parent::__construct($data);
    }
}
