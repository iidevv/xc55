<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\MultiVendor\Resolver\Product;

use XLite\Model\Repo;

/**
 * Class Products
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\MultiVendor")
 *
 */

class Products extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\Products
{
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        parent::prepareFilters($cnd, $filters);

        if (!empty($filters['vendorId'])) {
            $cnd->{Repo\Product::P_VENDOR_ID} = (int) $filters['vendorId'];
        }
    }
}
