<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductWholesalePriceOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\Choice({"price", "percent"})
     * @var string
     */
    public string $type = 'price';

    /**
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
