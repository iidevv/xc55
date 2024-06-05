<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\ProductsCarousel\View;

use XCart\Extender\Mapping\Extender;

/**
 * Recenly viewed products widget
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\ProductAdvisor","QSL\ProductsCarousel"})
 */
class ViewedBought extends \CDev\ProductAdvisor\View\ViewedBought
{
    use AdditionalCarouselDataAttributesTrait;
}
