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
 * Class ProductSpecificationGroupType
 * @package XcartGraphqlApi\Types\Model
 */
class ProductSpecificationGroupType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'product_specification_group',
            'description' => 'Product specification group',
            'fields'      => function () {
                return [
                    'label'      => Types::string(),
                    'items' => Types::listOf(Types::byName('specificationItem')),
                ];
            },
        ];
    }
}
