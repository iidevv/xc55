<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\Module\XC\ProductVariants\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product page widgets collection
 *
 * @Extender\Mixin
 * @Extender\After("XC\ProductVariants")
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        $widgets = parent::defineWidgetsCollection();

        if ($this->getProduct()->hasVariants()) {
            $widgets = array_merge(
                $widgets,
                ['Qualiteam\SkinActPinterestPixel\View\Product\Details\Customer\PinterestPixelPageVisit']
            );
        }

        return array_unique($widgets);
    }
}
