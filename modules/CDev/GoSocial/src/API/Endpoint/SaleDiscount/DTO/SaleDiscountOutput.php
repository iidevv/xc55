<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\SaleDiscount\DTO;

use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as ParentSaleDiscountOutputAlias;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class SaleDiscountOutput extends ParentSaleDiscountOutputAlias
{
    /**
     * @var bool
     */
    public bool $use_custom_open_graph = false;

    /**
     * @var string|null
     */
    public ?string $og_meta = null;
}
