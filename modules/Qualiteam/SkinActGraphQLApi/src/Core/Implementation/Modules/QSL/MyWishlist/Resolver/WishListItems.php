<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class WishListItems
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

class WishListItems extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\WishList\WishListItems
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
        $items = isset($val['items'])
            ? $val['items']
            : [];

        if (!$items) {
            return [];
        }

        return array_map(
            function ($product) {
                return $this->mapper->mapToDto($product);
            },
            $items
        );
    }
}
