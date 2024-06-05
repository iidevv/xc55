<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Product;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class ReorderAttributes extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'reorder_attributes',
            'description' => 'Reorder attributes',
            'fields'      => function () {
                return [
                    'name' => Types::string(),
                    'value' => Types::string(),
                    'attribute_id' => Types::int(),
                    'attribute_value_id' => Types::int(),
                ];
            },
        ];
    }
}