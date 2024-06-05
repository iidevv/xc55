<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Product;

use GraphQL\Type\Definition\ObjectType;
use XcartGraphqlApi\Types;

/**
 * Class ProductTag
 * @package XcartGraphqlApi\Types\Model\Product
 */
class ProductTag extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'        => 'productTag',
            'description' => 'Product tag',
            'fields'      => function () {
                return [
                    'id'            => Types::id(),
                    'name'          => Types::string(),
                    'position'      => Types::int()
                ];
            },
        ]);
    }
}
