<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\API\Endpoint\Product\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Input as ExtendedInput;

/**
 * @Extender\Mixin
 */
class ProductInput extends ExtendedInput
{
    /**
     * @var bool
     */
    public bool $participate_sale = false;

    /**
     * @Assert\Choice({"sale_price", "sale_percent"})
     * @var string
     */
    public string $discount_type = 'sale_percent';

    /**
     * @Assert\GreaterThanOrEqual("0")
     * @var float
     */
    public float $sale_price_value = 0.0;
}
