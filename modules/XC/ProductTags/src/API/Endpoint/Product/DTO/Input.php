<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Product\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Input extends \XLite\API\Endpoint\Product\DTO\Input
{
    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"={"new"}}
     *     }
     * )
     * @var string[]
     */
    public array $tags = [];
}
