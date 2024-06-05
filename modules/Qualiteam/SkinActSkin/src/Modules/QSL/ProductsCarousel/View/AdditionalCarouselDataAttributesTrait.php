<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\ProductsCarousel\View;

/**
 * Enables caching for a widget
 * @Extender\Depend ({"CDev\ProductAdvisor","QSL\ProductsCarousel"})
 */
trait AdditionalCarouselDataAttributesTrait
{
    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        $classes = parent::getBlockClasses();

        if ($this->isCarousel()) {
            $classes .= ' block-carousel-products';
        }

        return $classes;
    }
}
