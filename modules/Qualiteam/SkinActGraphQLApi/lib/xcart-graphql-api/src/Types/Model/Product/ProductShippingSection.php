<?php


namespace XcartGraphqlApi\Types\Model\Product;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class ProductShippingSection extends ObjectType
{

    protected function configure()
    {
        return [
            'name' => 'productShippingSection',
            'description' => 'Product shipping section',
            'fields' => function () {
                return [
                    'requiresShipping' => Types::boolean(),
                    'freeShipping' => Types::boolean(),
                    'localPickup' => Types::boolean(),
                    'separateBox' => Types::boolean(),
                ];
            },
        ];
    }
}