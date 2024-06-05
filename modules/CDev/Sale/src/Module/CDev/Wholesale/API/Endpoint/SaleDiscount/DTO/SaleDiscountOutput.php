<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\API\Endpoint\SaleDiscount\DTO;

use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as ParentSaleDiscountOutputAlias;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
class SaleDiscountOutput extends ParentSaleDiscountOutputAlias
{
    /**
     * @var bool
     */
    public bool $apply_to_wholesale = false;
}
