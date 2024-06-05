<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use QSL\MyWishlist\Core\Wishlist;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class MobileMenuButton extends \XLite\View\MobileMenuButton
{
    /**
     * Get the list of mobile menu button CSS classes.
     */
    public function getClass(): string
    {
        $classes = array_filter(
            array_map(
                static fn(string $class): string => mb_strtolower($class),
                explode(" ", parent::getClass())
            )
        );
        if (!in_array('recently-updated', $classes, true)) {
            $wishlist = Wishlist::getInstance()->getWishlist();
            if ($wishlist && $wishlist->getProductsCount() > 0) {
                $classes[] = 'recently-updated';
            }
        }
        return implode(' ', $classes);
    }
}
