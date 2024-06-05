<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\QSL\MyWishlist\Core\GA\JsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
class GTag extends \CDev\GoogleAnalytics\Core\GA\JsList\GTag
{
    protected function defineEcommerceJsList(): array
    {
        $list = parent::defineEcommerceJsList();

        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-wishlist.js';

        return $list;
    }
}
