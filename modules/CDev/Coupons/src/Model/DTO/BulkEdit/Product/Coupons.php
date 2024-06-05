<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Model\DTO\BulkEdit\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend ("XC\BulkEditing")
 */
class Coupons extends \XC\BulkEditing\Model\DTO\Product\AProduct
{
    public function __construct($data = null)
    {
        $this->scenario = 'coupons';

        parent::__construct($data);
    }
}
