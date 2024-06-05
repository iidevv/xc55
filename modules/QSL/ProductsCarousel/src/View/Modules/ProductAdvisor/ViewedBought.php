<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\Modules\ProductAdvisor;

use XCart\Extender\Mapping\Extender;
use QSL\ProductsCarousel\View\CarouselDataAttributesTrait;

/**
 * Recenly viewed products widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\ProductAdvisor")
 */
class ViewedBought extends \CDev\ProductAdvisor\View\ViewedBought
{
    use CarouselDataAttributesTrait;

    /**
     * @return string
     */
    protected function getBlockCode()
    {
        return "vb_carousel";
    }
}
