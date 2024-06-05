<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model\Repo;

/**
 * Wishlist repository
 */
class Wishlist extends \XLite\Model\Repo\ARepo
{
    /**
     * Wishlist getter
     * create wishlist if none is found
     *
     * @param mixed $id      Identificator
     * @param mixed $profile Profile model (optional)
     *
     * @return \QSL\MyWishlist\Model\Wishlist
     */
    public function getWishlist($id, $profile = null)
    {
        $profile = is_null($profile) ? \XLite\Core\Auth::getInstance()->getProfile() : $profile;

        $wishlist = is_null($id)
            ? $this->findOneBy([
                'customer' => $profile,
            ])
            : $this->find($id);

        if (
            !$wishlist
            && \XLite\Core\Auth::getInstance()->isWishlistAvailable()
        ) {
            $wishlist = new \QSL\MyWishlist\Model\Wishlist();
            $wishlist->setCustomer($profile);

            $wishlist = $this->insert($wishlist);
        }

        return $wishlist;
    }
}
