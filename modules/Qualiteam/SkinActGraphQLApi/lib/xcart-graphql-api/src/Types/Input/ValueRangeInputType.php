<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ValueRangeInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'value_range_input',
            'fields' => [
                'from' => [
                    'type' => Types::float(),
                    'description' => 'From value'
                ],
                'to' => [
                    'type' => Types::float(),
                    'description' => 'To value'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
