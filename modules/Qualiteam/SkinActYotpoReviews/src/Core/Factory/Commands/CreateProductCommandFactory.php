<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use XLite\Model\Product as ProductModel;
use Qualiteam\SkinActYotpoReviews\Core\Command\Create\Product;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Post\Create;

class CreateProductCommandFactory
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Post\Create $container
     */
    public function __construct(
        private Create $container
    ) {
    }

    public function createCommand(ProductModel $product): Product
    {
        return new Product($this->container, $product);
    }
}