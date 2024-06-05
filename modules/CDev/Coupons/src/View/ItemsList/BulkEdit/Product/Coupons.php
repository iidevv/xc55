<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\ItemsList\BulkEdit\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend ("XC\BulkEditing")
 */
class Coupons extends \XC\BulkEditing\View\ItemsList\BulkEdit\AProduct
{
    public function __construct(array $params)
    {
        $this->scenario = 'coupons';

        parent::__construct($params);
    }
}
