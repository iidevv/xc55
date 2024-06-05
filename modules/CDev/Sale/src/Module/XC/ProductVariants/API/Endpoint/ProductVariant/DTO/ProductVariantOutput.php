<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\DTO;

use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantOutput as ExtendedOutput;

/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants"})
 */
class ProductVariantOutput extends ExtendedOutput
{
    public string $sale_discount_type;

    public float $sale_price_value;

    public bool $default_sale_price;
}
