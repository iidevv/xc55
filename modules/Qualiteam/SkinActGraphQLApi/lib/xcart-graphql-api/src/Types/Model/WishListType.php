<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class WishListType
 * @package XcartGraphqlApi\Types\Model
 */
class WishListType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'wishlist',
            'description' => 'WishList model',
            'fields'      => function () {
                return [
                    'id'      => Types::id(),
                    'user_id' => Types::id(),
                    'count'   => Types::int(),
                    'items'   => [
                        'type'   => Types::listOf(Types::byName('product')),
                        'resolve' => $this->createResolveForType('wishlistItems'),
                    ]
                ];
            },
        ];
    }
}
