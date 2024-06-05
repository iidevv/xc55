<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ProductSelectedOptionInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name'   => 'SelectedOption',
            'description' => 'SelectedOption information model',
            'fields' => [
                'optionId'    => [
                    'type'        => Types::id(),
                    'description' => 'Option id'
                ],
                'value' => [
                    'type'        => Types::string(),
                    'description' => 'Option value'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
