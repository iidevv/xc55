<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\Category\DTO;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Output extends \XLite\API\Endpoint\Category\DTO\Output
{
    /**
     * @var string
     */
    public string $og_meta_tags;
}
