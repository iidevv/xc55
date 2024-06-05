<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\CloudSearch\Resolver\Product;

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
 * @Extender\Depend("QSL\CloudSearch")
 *
 */

class Products extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\Products
{
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        parent::prepareFilters($cnd, $filters);

        if (isset($filters['custom']) && is_array($filters['custom'])) {
            $cnd->{Product::P_CLOUD_FILTERS} = array_reduce(
                $filters['custom'],
                static function ($acc, $item) {
                    return array_merge($acc, json_decode($item, true));
                },
                []
            );

            $cnd->{Product::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH} = "Y";
        }
    }
}
