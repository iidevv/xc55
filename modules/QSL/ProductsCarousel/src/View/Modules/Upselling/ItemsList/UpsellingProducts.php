<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\Modules\Upselling\ItemsList;

use XCart\Extender\Mapping\Extender;
use QSL\ProductsCarousel\View\CarouselDataAttributesTrait;

/**
 * Related products widget (customer area)
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\Upselling")
 */
class UpsellingProducts extends \XC\Upselling\View\ItemsList\UpsellingProducts
{
    use CarouselDataAttributesTrait;

    /**
     * @return string
     */
    protected function getBlockCode()
    {
        return "us_carousel";
    }
}
