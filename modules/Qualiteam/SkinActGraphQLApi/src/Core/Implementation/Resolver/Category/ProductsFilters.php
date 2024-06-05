<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class ProductsFilters
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product
 */
class ProductsFilters implements ResolverInterface
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return [
            $this->getProductSearchFilter(),
            $this->getProductOrderFilter(),
        ];
    }

    /**
     * Get orderBy filter
     *
     * @return array
     */
    protected function getProductOrderFilter()
    {
        $values = \XLite\View\ItemsList\Product\Customer\Search::getInstance()->getSortByFieldsForMobileApi();

        return [
            'name'      => 'orderByFilter',
            'label'     => (string) \XLite\Core\Translation::lbl('Order by'),
            'type_name' => 'selectField',
            'type'      => 'FIELD_TYPE_SELECT',
            'defaultValue' => array_keys($values)[0],
            'options'   => $values,
            'required'  => false,
            'multiple'  => false,
        ];
    }

    /**
     * Get search filter
     *
     * @return array
     */
    protected function getProductSearchFilter()
    {
        return [
            'name'      => 'searchFilter',
            'label'     => (string) \XLite\Core\Translation::lbl('Search'),
            'type_name' => 'textField',
            'type'      => 'FIELD_TYPE_SEARCH',
            'defaultValue' => '',
        ];
    }
}
