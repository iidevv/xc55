<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Traits;


use XcartGraphqlApi\Types;

trait ProductsFilterTrait
{
    public function defineFields()
    {
        return [
            'searchFilter'  => [
                'type'        => Types::string(),
                'description' => 'Search query string',
            ],
            'stockFilter'   => [
                'type'        => Types::boolean(),
                'description' => 'Search products by stock status',
            ],
            'custom'        => [
                'type'        => Types::listOf(Types::string()),
                'description' => 'Custom json-encoded filters',
            ],
            'categoryId'    => [
                'type'        => Types::id(),
                'description' => 'Category id to filter',
            ],
            'inSubcats'     => [
                'type'        => Types::boolean(),
                'description' => 'Search in subcategories',
            ],
            'bestsellers'   => [
                'type'        => Types::boolean(),
                'description' => 'Search bestsellers',
            ],
            'featured'      => [
                'type'        => Types::boolean(),
                'description' => 'Search featured products',
            ],
            'new_arrivals'      => [
                'type'        => Types::boolean(),
                'description' => 'Search new arrived products',
            ],
            'sale'      => [
                'type'        => Types::boolean(),
                'description' => 'Search products on sale',
            ],
            'enabled'       => [
                'type'        => Types::boolean(),
                'description' => 'Search enabled products',
            ],
            'removeEnabledCnd'       => [
                'type'        => Types::boolean(),
                'description' => 'Remove product is enabled cnd while searching',
            ],
        ];
    }
}