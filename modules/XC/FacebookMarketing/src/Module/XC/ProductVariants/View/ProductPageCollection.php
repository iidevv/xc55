<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Module\XC\ProductVariants\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product page widgets collection
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
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
                ['XC\FacebookMarketing\View\Product\Details\Customer\PixelValue']
            );
        }

        return array_unique($widgets);
    }
}
