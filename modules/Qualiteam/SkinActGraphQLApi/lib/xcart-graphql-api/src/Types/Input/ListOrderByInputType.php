<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ListOrderByInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'list_order_filter',
            'fields' => [
                'name' => [
                    'type' => Types::string(),
                    'description' => 'Property name'
                ],
                'order' => [
                    'type' => Types::string(),
                    'description' => 'Order: asc or desc'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
