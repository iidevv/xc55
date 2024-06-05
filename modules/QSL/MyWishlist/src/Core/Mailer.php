<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core;

use XCart\Extender\Mapping\Extender;
use QSL\MyWishlist\Core\Mail\WishlistMessage;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    public static function sendWishlist($email, $wishlist)
    {
        return (new WishlistMessage($email, $wishlist))->send();
    }
}
