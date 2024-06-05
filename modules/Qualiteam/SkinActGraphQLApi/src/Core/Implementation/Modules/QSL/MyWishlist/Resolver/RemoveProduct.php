<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Resolver;

use Doctrine\ORM\ORMException;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class RemoveProduct
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

class RemoveProduct extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\WishList\RemoveProduct
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

        $wishList = $this->getWishList();

        /** @var \QSL\MyWishlist\Model\WishlistLink $link */
        foreach ($wishList->getWishlistLinks() as $link) {
            if ($link->getParentProduct()) {
                if ($link->getParentProduct()->getProductId() == $args['product_id']) {
                    $wishList->removeWishlistLink($link->getUniqueIdentifier());
                }
            } else {
                $wishList->removeWishlistLink($link->getUniqueIdentifier());
            }
        }

        \XLite\Core\Database::getEM()->persist($wishList);
        try {
            \XLite\Core\Database::getEM()->flush($wishList);
        } catch (ORMException $e) {
            throw new UserError('Couldn\'t add product');
        }

        return $this->mapWishlist($wishList);
    }

    /**
     * @return \QSL\MyWishlist\Model\Wishlist
     */
    protected function getWishList()
    {
        return \XLite\Core\Database::getRepo('\QSL\MyWishlist\Model\Wishlist')
            ->getWishlist(null);
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
