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
 * Class ProductOptionType
 * @package XcartGraphqlApi\Types\Model
 */
class ProductAttributeGroupType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'product_attribute_group',
            'description' => 'Product attribute group',
            'fields'      => function () {
                return [
                    'id'         => Types::id(),
                    'label'      => Types::string(),
                    'pos'        => Types::int(),
                    'attributes' => Types::listOf(Types::byName('productAttribute')),
                ];
            },
        ];
    }
}
