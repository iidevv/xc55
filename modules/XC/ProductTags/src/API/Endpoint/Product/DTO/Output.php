<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Product\DTO;

use XCart\Extender\Mapping\Extender;
use XC\ProductTags\API\Endpoint\Tag\DTO\TagOutput as ProductTagOutput;

/**
 * @Extender\Mixin
 */
class Output extends \XLite\API\Endpoint\Product\DTO\Output
{
    /**
     * @var ProductTagOutput[]
     */
    public array $tags;
}
