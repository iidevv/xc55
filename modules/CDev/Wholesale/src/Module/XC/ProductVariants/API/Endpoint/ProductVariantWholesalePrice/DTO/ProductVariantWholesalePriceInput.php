<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariantWholesalePriceInput
{
    /**
     * @Assert\Choice({"price", "percent"})
     * @var string
     */
    public string $type = 'price';

    /**
     * @Assert\GreaterThanOrEqual("0")
     * @var float
     */
    public float $price = 0.0;

    /**
     * @Assert\GreaterThan("1")
     * @var int
     */
    public int $quantity_range_begin = 2;

    /**
     * @var int|null
     */
    public ?int $membership = null;
}
