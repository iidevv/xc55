<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * QuickLook
 * @Extender\Mixin
 */
class QuickLook extends \XLite\View\Product\Details\Customer\Page\QuickLook
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductVariants/product/controller_quicklook.js';

        return $list;
    }

    /**
     * Should we use product image
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \XLite\Model\Image\Product\Image
     */
    public function getQuicklookImage(\XLite\Model\Product $product)
    {
        return $product->getProductImage()
            ?: $product->getImage();
    }
}
