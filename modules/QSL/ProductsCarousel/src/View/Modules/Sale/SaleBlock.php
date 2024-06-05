<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\Modules\Sale;

use XCart\Extender\Mapping\Extender;
use QSL\ProductsCarousel\View\CarouselDataAttributesTrait;

/**
 * Sale products block widget
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class SaleBlock extends \CDev\Sale\View\SaleBlock
{
    use CarouselDataAttributesTrait;

    /**
     * @return string
     */
    protected function getBlockCode()
    {
        return "sale_carousel";
    }
}
