<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Resolver;


use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XLite\Core\Database;
use XLite\Model\Cart;
use XLite\Model\Profile;

/**
 * Class MenuNotificationsCustomer
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Resolver
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

class MenuNotificationsCustomer extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\MenuNotificationsCustomer
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $profile = $context->getLoggedProfile();

        return $this->prepareResult($context, [
            'cart'          => $profile ? $this->getCartCount($profile) : 0,
            'wishlist'      => $profile ? $this->getWishlistProductsCount($profile) : 0
        ]);
    }

    protected function getCartCount(Profile $profile)
    {
        return $this->getCart($profile) && $this->getCart($profile)->getItems()
            ? $this->getCart($profile)->getItems()->count()
            : 0;
    }

    protected function getCart(Profile $profile)
    {
        return Database::getRepo(Cart::class)
            ->findOneBy([
                'orig_profile' => $profile,
            ]);
    }

    protected function getWishlistProductsCount(Profile $profile)
    {
        return $this->getWishlist($profile) ? $this->getWishlist($profile)->getProductsCount() : 0;
    }

    protected function getWishlist(Profile $profile)
    {
        return \XLite\Core\Database::getRepo('\QSL\MyWishlist\Model\Wishlist')
            ->getWishlist(null, $profile);
    }
}