<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Wishlist products page controller
 * @Extender\Mixin
 */
class AAdmin extends \XLite\Controller\Admin\AAdmin
{
    public function isWishlistLink()
    {
        return false;
    }
}
