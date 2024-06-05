<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Gallery
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class Gallery extends \XLite\View\Product\Details\Customer\Gallery
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/ProductVariants/product/cycle-gallery.js'
        ]);
    }
}
