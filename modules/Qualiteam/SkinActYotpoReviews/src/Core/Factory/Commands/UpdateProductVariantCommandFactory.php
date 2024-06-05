<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use Qualiteam\SkinActYotpoReviews\Core\Command\Update\ProductVariant;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch\Update;

class UpdateProductVariantCommandFactory
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch\Update $container
     */
    public function __construct(
        private Update $container
    ) {
    }

    public function createCommand(): ProductVariant
    {
        return new ProductVariant($this->container);
    }
}
