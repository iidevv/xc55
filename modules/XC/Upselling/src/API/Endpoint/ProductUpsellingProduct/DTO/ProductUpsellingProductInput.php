<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductUpsellingProductInput
{
    /**
     * @Assert\Positive()
     * @var integer
     */
    public int $product_id = 0;

    /**
     * @var integer
     */
    public int $position = 0;
}
