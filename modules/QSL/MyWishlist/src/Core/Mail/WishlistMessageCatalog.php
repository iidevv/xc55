<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * Mailer changes if Catalog module is on
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\Catalog", "QSL\MyWishlist"})
 */
abstract class WishlistMessageCatalog extends WishlistMessage
{
    public static function getDir()
    {
        return 'modules/QSL/MyWishlist/send_wishlist';
    }
}
