<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\Modules\FeaturedProducts\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\ProductsCarousel\View\CarouselDataAttributesTrait;

/**
 * Featured products widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\FeaturedProducts")
 */
class FeaturedProducts extends \CDev\FeaturedProducts\View\Customer\FeaturedProducts
{
    use CarouselDataAttributesTrait;

    /**
     * @return string
     */
    protected function getBlockCode()
    {
        return "fp_carousel";
    }
}
