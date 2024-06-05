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
 * Class SellerType
 * @package XcartGraphqlApi\Types\Model
 */
class SellerType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'seller',
            'description' => 'Seller information model',
            'fields'      => function () {
                return [
                    'id'                => Types::id(),
                    'earned_in_month'   => Types::float(), //ask joy
                    'plan'              => Types::byName('vendorPlan'),
//                    'orders'            => Types::listOf(Types::byName('order')),
                    'products'       => [
                        'type'    => Types::listOf(Types::byName('product')),
                        'args' => [
                            'from' => [
                                'type' => Types::int(),
                                'description' => 'Subset from'
                            ],
                            'size' => [
                                'type' => Types::int(),
                                'description' => 'Subset size'
                            ],
                            'filters' => [
                                'type'        => Types::byName('productsFiltersInput'),
                                'description' => 'Filters'
                            ],
                        ]
                    ],
                    'conversations'     => Types::listOf(Types::byName('conversation')),
                    'questions'         => Types::listOf(Types::byName('question')),
                    'leftProductsCount' => Types::int(),
                    'newInMonthCount'   => Types::int(),
                ];
            },
        ];
    }
}
