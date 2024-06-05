<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Cart;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class OrderItemType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'orderItem',
            'description' => 'OrderItem model',
            'fields'      => function () {
                return [
                    'id'                => Types::id(),
                    'product'           => Types::byName('product'),
                    'sku'               => Types::string(),
                    'name'              => Types::string(),
                    'price'             => Types::float(),
                    'amount'            => Types::int(),
                    'total'             => Types::float(),
                    'options'           => Types::listOf(Types::byName('orderItemOption')),
                    'taxes'             => Types::listOf(Types::string()), // TODO Should be object type?
                    'is_booking'        => Types::boolean(),
                    'date_from'         => Types::string(),
                    'date_to'           => Types::string(),
                ];
            },
        ];
    }
}
