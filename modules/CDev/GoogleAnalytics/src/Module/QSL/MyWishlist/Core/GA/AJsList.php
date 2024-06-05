<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\QSL\MyWishlist\Core\GA;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
class AJsList extends \CDev\GoogleAnalytics\Core\GA\AJsList
{
    protected function defineEcommerceJsList(): array
    {
        $list = parent::defineEcommerceJsList();

        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-wishlist.js';

        return $list;
    }
}
