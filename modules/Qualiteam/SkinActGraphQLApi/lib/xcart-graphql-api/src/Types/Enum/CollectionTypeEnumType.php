<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Enum;

use GraphQL\Type\Definition\EnumType;
use XcartGraphqlApi\DTO\UserDTO;

class CollectionTypeEnumType extends EnumType
{
    /**
     * EnumType constructor.
     */
    public function __construct()
    {
        $config = [
            'name'        => 'collection_type',
            'description' => 'Collection type enumeration',
            'values'      => [
                'products'            => [
                    'value'       => 'products',
                    'description' => 'Products collection'
                ],
                'categories'   => [
                    'value'       => 'categories',
                    'description' => 'Categories collection'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
