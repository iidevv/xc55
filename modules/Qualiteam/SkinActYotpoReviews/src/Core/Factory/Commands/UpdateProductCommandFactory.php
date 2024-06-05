<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use XLite\Model\Product as ProductModel;
use Qualiteam\SkinActYotpoReviews\Core\Command\Update\Product;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Patch\Update;

class UpdateProductCommandFactory
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Patch\Update $container
     */
    public function __construct(
        private Update $container
    ) {
    }

    public function createCommand(ProductModel $product): Product
    {
        return new Product($this->container, $product);
    }
}
