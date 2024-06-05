<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Gallery
 * @Extender\Mixin
 */
class Gallery extends \XLite\View\Product\Details\Customer\Gallery
{
    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $product = $this->getProduct();
        $repo = \XLite\Core\Database::getRepo('\XC\ProductVariants\Model\Image\ProductVariant\Image');

        return parent::isVisible()
            || ($product->hasVariants() && $repo->countByProduct($product));
    }


    /**
     * Get list item class name
     *
     * @param integer                 $i     Detailed image index
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return array
     */
    protected function getListItemClass($i, \XLite\Model\Base\Image $image)
    {
        $classes = parent::getListItemClass($i, $image);
        if ($image instanceof \XC\ProductVariants\Model\Image\ProductVariant\Image) {
            $classes[] = 'variant-image';
        }

        return $classes;
    }
}
