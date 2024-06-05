<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\ProductsCarousel\View;

use QSL\ProductsCarousel\View\CarouselDataAttributesTrait;
use XCart\Extender\Mapping\Extender;

/**
 * Recently viewed products widget
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\ProductAdvisor","QSL\ProductsCarousel"})
 */
class RecentlyViewed extends \CDev\ProductAdvisor\View\RecentlyViewed
{
    use CarouselDataAttributesTrait;
    use AdditionalCarouselDataAttributesTrait;

    /**
     * @return string
     */
    protected function getBlockCode()
    {
        return "rv_carousel";
    }

}
