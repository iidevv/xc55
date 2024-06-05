<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\DTO;

use CDev\Sale\Model\Product;
use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantInput as ExtendedInput;

/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants"})
 */
class ProductVariantInput extends ExtendedInput
{
    /**
     * @Assert\Choice({"sale_price", "sale_percent"})
     * @var string
     */
    public string $sale_discount_type = Product::SALE_DISCOUNT_TYPE_PERCENT;

    /**
     * @Assert\PositiveOrZero()
     * @var float
     */
    public float $sale_price_value = 0.0;
}
