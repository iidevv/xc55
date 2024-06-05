<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Category;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class CategoryType
 * @package XcartGraphqlApi\Types\Model
 */
class CategoryType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'category',
            'description' => 'Category model',
            'interfaces'  => [
                Types::byName('collection_item')
            ],
            'fields'      => function () {
                return [
                    'id'         => Types::id(),
                    'category_name'       => Types::string(),
                    'description'         => Types::string(),
                    'category_url'        => Types::string(),
                    'image_url'           => Types::string(),
                    'banner_url'          => Types::string(),
                    'parent_id'           => Types::id(),
                    'products_count'      => Types::int(),
                    'subcategories_count' => Types::int(),
                    'subcategories'       => [
                        'type'    => Types::listOf(Types::byName('category')),
                        'resolve' => $this->createResolveForType('subcategories'),
                        'args' => [
                            'from' => [
                                'type' => Types::int(),
                                'description' => 'Subset from'
                            ],
                            'size' => [
                                'type' => Types::int(),
                                'description' => 'Subset size'
                            ],
                        ]
                    ],
                    'products'       => [
                        'type'    => Types::listOf(Types::byName('product')),
                        'resolve' => $this->createResolveForType('categoryProducts'),
                        'args' => [
                            'from' => [
                                'type' => Types::int(),
                                'description' => 'Subset from'
                            ],
                            'size' => [
                                'type' => Types::int(),
                                'description' => 'Subset size'
                            ],
                            'filters' => [
                                'type'        => Types::byName('productsFiltersInput'),
                                'description' => 'Filters'
                            ],
                        ]
                    ],
                    'filters' => [
                        'type' => Types::listOf(Types::byName('categoryFilters')),
                        'resolve' => $this->createResolveForType('categoryProductsFilters'),
                    ]
                ];
            },
        ];
    }
}
