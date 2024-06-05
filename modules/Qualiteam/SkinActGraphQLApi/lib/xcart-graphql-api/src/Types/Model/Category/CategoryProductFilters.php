<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Category;

use GraphQL\Type\Definition\UnionType;
use XcartGraphqlApi\Types;

/**
 * Class CategoryProductFilters
 * @package XcartGraphqlApi\Types\Model\Category
 */
class CategoryProductFilters extends UnionType
{
    public function __construct()
    {
        parent::__construct([
            'name'        => 'category_products_filter',
            'description' => 'Category products filter model',
            'types'      => [
                Types::byName('selectField'),
                Types::byName('switchField'),
                Types::byName('textField'),
                Types::byName('valueRangeField'),
            ],
            'resolveType' => function($value) {
                switch ($value['type']) {
                    case 'FIELD_TYPE_SELECT':
                        return Types::byName('selectField');

                    case 'FIELD_TYPE_SWITCH':
                        return Types::byName('switchField');

                    case 'FIELD_TYPE_VALUE_RANGE':
                        return Types::byName('valueRangeField');

                    default:
                        return Types::byName('textField');
                }
            }
        ]);
    }
}
