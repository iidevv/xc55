<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Strategy\UpdateProduct;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use Qualiteam\SkinActYotpoReviews\Helpers\CreateUpdate\IUpdate;
use XCart\Container;
use XLite\Model\Product;

class UpdateProduct
{
    private IUpdate $strategy;

    public function __construct(
        private Product $product
    )
    {
        $this->strategy = $this->isDevMode()
            ? new Dev($this->product)
            : new Prod($this->product);
    }

    public function execute()
    {
        $this->strategy->execute();
    }

    protected function isDevMode(): bool
    {
        return $this->getConfigContainer()?->isDevMode();
    }

    protected function getConfigContainer(): ?Configuration
    {
        return Container::getContainer()?->get('yotpo.reviews.configuration');
    }
}