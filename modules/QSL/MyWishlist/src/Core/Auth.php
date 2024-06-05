<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Auth
 * @Extender\Mixin
 */
abstract class Auth extends \XLite\Core\Auth
{
    /**
     * Wishlist available only for the logged in and registered users
     *
     * @return boolean
     */
    public function isWishlistAvailable()
    {
        return $this->isLogged() && !$this->isAnonymous();
    }
}
