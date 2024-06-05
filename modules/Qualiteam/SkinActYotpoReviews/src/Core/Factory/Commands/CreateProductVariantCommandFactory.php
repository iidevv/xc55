<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use Qualiteam\SkinActYotpoReviews\Core\Command\Create\ProductVariant;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Post\Create;

class CreateProductVariantCommandFactory
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Post\Create $container
     */
    public function __construct(
        private Create $container
    ) {
    }

    public function createCommand(): ProductVariant
    {
        return new ProductVariant($this->container);
    }
}
