<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Controller\Customer;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XLite\Controller\Customer\ACustomer;
use XLite\Core\Database;
use XLite\Model\Product;

class ProductVideoTab extends ACustomer
{
    use VideoTourTrait;

    /**
     * getViewerTemplate
     *
     * @return string
     */
    protected function getViewerTemplate()
    {
        return $this->getModulePath() . '/product_video_tab.twig';
    }

    public function getProduct()
    {
        static $product = null;

        if ($product === null) {
            $product = Database::getRepo(Product::class)?->find($this->getProductId());
        }

        return $product;
    }

    protected function isVisible(): bool
    {
        return parent::isVisible()
            && $this->getProductId();
    }
}
