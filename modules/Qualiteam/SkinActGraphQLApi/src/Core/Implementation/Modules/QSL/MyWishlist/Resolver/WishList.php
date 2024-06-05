<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Resolver;


use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class WishList
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\MyWishlist")
 *
 */

class WishList extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\WishList\WishList
{
    /**
     * @param                                    $val
     * @param                                    $args
     * @param XCartContext                       $context
     * @param ResolveInfo                        $info
     *
     * @return mixed
     * @throws \GraphQL\Error\UserError
     * @throws \Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if (!$context->isAuthenticated()) {
            throw new AccessDenied();
        }

        $wishlist = \XLite\Core\Database::getRepo('\QSL\MyWishlist\Model\Wishlist')
            ->getWishlist(null);

        if (!$wishlist) {
            throw new UserError('No wishlist found');
        }

        return $this->mapWishlist($wishlist);
    }

    /**
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist
     *
     * @return array
     */
    public function mapWishlist($wishlist)
    {
        $mapper = new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Mapper\WishList();

        return $mapper->mapWishlist($wishlist);
    }
}
