<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\API\Endpoint\Product\DTO;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Output extends \XLite\API\Endpoint\Product\DTO\Output
{
    public bool $facebook_marketing_enabled;
}
