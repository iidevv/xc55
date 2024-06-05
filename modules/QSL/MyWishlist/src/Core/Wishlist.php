<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Wishlist
 */
class Wishlist extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    protected $wishlists = [];

    /**
     * Return the wishlist model (or even create one if none is found)
     *
     * @param mixed $id      Wishlist identificator
     * @param mixed $profile Profile model
     * @param string $hash   Hash string
     *
     * @return \QSL\MyWishlist\Model\Wishlist
     */
    public function getWishlist($id = null, $profile = null, $hash = null)
    {
        if ($hash && !array_key_exists($hash, $this->wishlists)) {
            $this->wishlists[$hash] = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\Wishlist')
                ->findOneBy(['hash' => $hash]);
        } elseif (!array_key_exists($id, $this->wishlists)) {
            $this->wishlists[$id] = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\Wishlist')
                ->getWishlist($id, $profile);
        }

        return $this->wishlists[$hash ?: $id];
    }

    public function getWishlistProductIds()
    {
        return $this->executeCachedRuntime(function () {
            return \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\WishlistLink')->getWishlistProductIds($this->getWishlist());
        });
    }
}
