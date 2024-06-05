<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductUpsellingProductOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\Positive()
     * @var integer
     */
    public int $product_id;

    /**
     * @var integer
     */
    public int $position = 0;
}
