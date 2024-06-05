<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\API\Endpoint\Product\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Input extends \XLite\API\Endpoint\Product\DTO\Input
{
    /**
     * @Assert\PositiveOrZero()
     * @var float
     */
    public float $market_price = 0.0000;
}
