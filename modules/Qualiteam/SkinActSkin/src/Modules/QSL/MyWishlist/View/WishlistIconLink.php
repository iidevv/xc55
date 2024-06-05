<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\MyWishlist\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Wishlist Header Icon
 *
 * @ListChild(list="layout.header.right", weight="75", zone="customer")
 */
class WishlistIconLink extends \QSL\MyWishlist\View\WishlistLink
{

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/button/header.bar.wishlist-icon.twig';
    }


    /**
     * CSS class of wishlist link
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
            . ' header-icon-menu';
    }

}
