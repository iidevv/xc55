<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\Product\DTO;

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
    public bool $apply_sale_to_wholesale = false;
}
