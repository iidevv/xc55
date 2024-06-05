<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ProductsFilterInputType extends InputObjectType
{
    use Types\Traits\CollectionItemFilterTrait;

    public function configure()
    {
        return [
            'name'   => 'products_filter_input',
            'description' => 'Products filter input',
            'fields' => $this->defineFields()
        ];
    }

    protected function defineFields()
    {
        return array_merge($this->defineCommonFields(), [
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
                'description' => 'Search products on sale. Requires CDev-Sale addon.',
            ],
            'tagFilter'   => [
                'type'        => Types::string(),
                'description' => 'Search products by tag. Requires XC-ProductTags addon.',
            ],
            'vendorId'   => [
                'type'        => Types::id(),
                'description' => 'Search products by vendor id. Requires XC-MultiVendor addon.',
            ],
            'priceFilter'   => [
                'type'        => Types::byName('valueRangeInputType'),
                'description' => 'Search products by stock status',
            ],
            'orderByFilter' => [
                'type'        => Types::byName('listOrderByInputType'),
                'description' => 'Order by',
            ],
            'removeEnabledCnd'       => [
                'type'        => Types::boolean(),
                'description' => 'Remove product is enabled cnd while searching',
            ],
        ]);
    }

    public function __construct()
    {
        $config = $this->configure();

        parent::__construct($config);
    }
}
