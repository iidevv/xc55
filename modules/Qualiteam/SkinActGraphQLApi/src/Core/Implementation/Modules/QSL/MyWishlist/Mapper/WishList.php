<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Mapper;

/**
 * Class WishList
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Mapper
 */
class WishList
{
    /**
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist
     *
     * @return array
     */
    public function mapWishlist($wishlist)
    {
        $items = [];
        $count = 0;

        /** @var \QSL\MyWishlist\Model\WishlistLink $link */
        foreach ($wishlist->getWishlistLinks() as $link) {
            $product = $link->getParentProduct();

            if ($product && $product->getEnabled()) {
                $items[] = $product;
                $count++;
            }
        }

        return [
            'id'      => $wishlist->getId(),
            'user_id' => $wishlist->getCustomer()->getProfileId(),
            'count'   => $count,
            'items'   => $items,
        ];
    }
}
