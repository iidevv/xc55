<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ProductBatchLineType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'product_batch_line_input',
            'fields' => [
                'id' => [
                    'type' => Types::id(),
                    'description' => 'Product id'
                ],
                'amount' => [
                    'type' => Types::int(),
                    'description' => 'Product amount'
                ],
                'attributes' => [
                    'type' => Types::listOf(Types::string()),
                    'description' => 'Product attributes'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
