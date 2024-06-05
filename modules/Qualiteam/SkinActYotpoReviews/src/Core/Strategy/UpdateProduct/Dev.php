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

class Dev implements IUpdate
{
    public function __construct(
        private Product $product
    )
    {
    }

    public function execute(): void
    {
        if (!$this->isSkipProduct()) {
            $prod = new Prod($this->product);
            $prod->execute();
        }
    }

    protected function getProductDevPrefix(): string
    {
        return $this->getConfigContainer()->getProductDevPrefix();
    }

    protected function getConfigContainer(): ?Configuration
    {
        return Container::getContainer()?->get('yotpo.reviews.configuration');
    }

    protected function isSkipProduct(): bool
    {
        return !str_contains($this->product->getName(), $this->getProductDevPrefix());
    }
}