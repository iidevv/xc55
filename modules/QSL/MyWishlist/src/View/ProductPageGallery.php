<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\Extender;

/**
 * ProductPageGallery
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class ProductPageGallery extends \XLite\View\Product\Details\Customer\Gallery
{
    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getProduct()->isSnapshotProduct() ? false : parent::isVisible();
    }
}
