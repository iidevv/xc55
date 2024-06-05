<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\FeaturedProducts\Resolver\Product;
use XLite\Model\Repo\Product;

/**
 * Class Products
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("CDev\FeaturedProducts")
 *
 */

class Products extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\Products
{
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        parent::prepareFilters($cnd, $filters);

        if (isset($filters['featured']) && $filters['featured'] === true) {
            if (isset($cnd->{Product::P_SEARCH_IN_SUBCATS})) {
                unset($cnd->{Product::P_SEARCH_IN_SUBCATS});
            }

            if (isset($cnd->{Product::P_CATEGORY_ID})) {
                unset($cnd->{Product::P_CATEGORY_ID});
            }

            if (isset($cnd->{Product::P_ORDER_BY})) {
                unset($cnd->{Product::P_ORDER_BY});
            }

            $cnd->{Product::SEARCH_FEATURED_CATEGORY_ID} = isset($filters['categoryId'])
                ? $filters['categoryId']
                : \XLite\Core\Database::getRepo('XLite\Model\Category')
                    ->getRootCategoryId();
        }
    }
}
