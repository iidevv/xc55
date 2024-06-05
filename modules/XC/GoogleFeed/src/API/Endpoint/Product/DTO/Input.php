<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\API\Endpoint\Product\DTO;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Input extends \XLite\API\Endpoint\Product\DTO\Input
{
    /**
     * @var bool
     */
    public bool $google_feed_enabled = true;
}
