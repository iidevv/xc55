<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Search profiles
 * @Extender\Mixin
 */
abstract class Profile extends \XLite\View\ItemsList\Model\Profile
{
    protected $wishlistCount = [];

    /**
     * Define list columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['my_wishlist'] = [
            static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Wishlist'),
            static::COLUMN_TEMPLATE => 'modules/QSL/MyWishlist/items_list/model/profile/cell.wishlist.twig',
            static::COLUMN_ORDERBY  => 450,
        ];

        return $columns;
    }

    /**
     * Define the first wishlist for profile
     *
     * @param \XLite\Model\Profile $profile Profile model
     *
     * @return mixed
     */
    protected function getWishlist($profile)
    {
        return \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\Wishlist')
            ->findOneBy(['customer' => $profile]);
    }

    /**
     * Define the wishlist's products count for profile
     *
     * @param \XLite\Model\Profile $profile Profile model
     *
     * @return integer
     */
    protected function getWishlistCount($profile)
    {
        $profileId = $profile->getProfileId();

        if (!isset($this->wishlistCount[$profileId])) {
            $wishlist = $this->getWishlist($profile);

            $this->wishlistCount[$profileId] = $wishlist ? $wishlist->getWishlistLinks()->count() : 0;
        }

        return $this->wishlistCount[$profileId];
    }
}
