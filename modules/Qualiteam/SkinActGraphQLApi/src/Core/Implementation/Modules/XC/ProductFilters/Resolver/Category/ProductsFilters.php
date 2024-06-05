<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\ProductFilters\Resolver\Category;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;

/**
 * ProductsFilters
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\ProductFilter")
 *
 */

class ProductsFilters extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category\ProductsFilters
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
        return array_filter(
            array_merge(
                parent::__invoke($val, $args, $context, $info),
                [
                    $this->getProductPriceFilter($val->id),
                    $this->getProductStockFilter(),
                ]
            )
        );
    }


    /**
     * Get orderBy filter
     *
     * @return array
     */
    protected function getProductPriceFilter($categoryId)
    {
        // TODO This should be a clearer way to do this
        $request = \XLite\Core\Request::getInstance();
        $data = $request->getNonFilteredData();
        $data['category_id'] = $categoryId;
        $request->mapRequest($data);

        $data = [
            'name'      => 'priceFilter',
            'label'     => (string) \XLite\Core\Translation::lbl('Price range'),
            'type'      => 'FIELD_TYPE_VALUE_RANGE',
            'options'   => [],
            'required'  => false,
            'minValue'  => (float) \XC\ProductFilter\View\Filter\PriceRange::getInstance()->getMinPrice(),
            'maxValue'  => (float) \XC\ProductFilter\View\Filter\PriceRange::getInstance()->getMaxPrice(),
        ];

        return $data['min'] === $data['max'] ? null : $data;
    }

    /**
     * Get orderBy filter
     *
     * @return array
     */
    protected function getProductStockFilter()
    {
        return [
            'name'      => 'stockFilter',
            'label'     => (string) \XLite\Core\Translation::lbl('In stock only'),
            'type'      => 'FIELD_TYPE_SWITCH',
            'defaultValue' => true,
            'required'  => false,
        ];
    }
}
