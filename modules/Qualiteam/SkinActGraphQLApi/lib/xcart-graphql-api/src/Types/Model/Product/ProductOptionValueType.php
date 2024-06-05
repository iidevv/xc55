<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Product;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class ProductType
 * @package XcartGraphqlApi\Types\Model
 */
class ProductOptionValueType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'product_option_value',
            'description' => 'Product option value',
            'fields'      => function () {
                return [
                    'id'             => Types::id(),
                    'value'          => Types::string(),
                    'default'        => Types::boolean(),
                    'modifier_type'  => Types::string(),
                    'modifier_value' => Types::float(),
                ];
            },
        ];
    }
}
