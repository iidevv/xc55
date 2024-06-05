<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Sale\Resolver\Product;
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
 * @Extender\Depend("CDev\Sale")
 *
 */

class Products extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\Products
{
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        parent::prepareFilters($cnd, $filters);

        if (isset($filters['sale']) && $filters['sale'] === true) {
            $cnd->{Product::P_CATEGORY_ID}         = 0;
            $cnd->{Product::P_SEARCH_IN_SUBCATS}   = true;
            $cnd->{Product::P_PARTICIPATE_SALE}    = true;
            $cnd->{Product::P_ORDER_BY}            = [ \XLite\Model\Repo\Product::PERCENT_CALCULATED_FIELD, 'desc' ];
        }
    }
}
