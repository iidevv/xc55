<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\Modules\Bestsellers;

use XCart\Extender\Mapping\Extender;
use QSL\ProductsCarousel\View\CarouselDataAttributesTrait;

/**
 * Bestsellers widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Bestsellers")
 */
class Bestsellers extends \CDev\Bestsellers\View\Bestsellers
{
    use CarouselDataAttributesTrait;

    /**
     * @return string
     */
    protected function getBlockCode()
    {
        return "b_carousel";
    }
}
