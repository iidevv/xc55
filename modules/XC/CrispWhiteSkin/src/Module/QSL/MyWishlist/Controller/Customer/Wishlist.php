<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
abstract class Wishlist extends \QSL\MyWishlist\Controller\Customer\Wishlist
{
    /**
     * @return string
     */
    public function getTitle()
    {
        $wishlistItemQuantity = $this->getWishlist()->getWishlistLinks()->count();

        return parent::getTitle() . ($wishlistItemQuantity > 0 ? ' - ' . $wishlistItemQuantity : '');
    }
}
