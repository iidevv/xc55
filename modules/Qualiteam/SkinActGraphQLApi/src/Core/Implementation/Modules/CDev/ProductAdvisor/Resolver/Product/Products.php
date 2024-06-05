<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\ProductAdvisor\Resolver\Product;

use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Model\Repo\Product;
use CDev\ProductAdvisor\Main;
use CDev\ProductAdvisor\View\ItemsList\Product\Customer\ACustomer;

/**
 * Class Products
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("CDev\ProductAdvisor")
 *
 */

class Products extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\Products
{
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        parent::prepareFilters($cnd, $filters);

        if (isset($filters['new_arrivals']) && $filters['new_arrivals'] === true) {
            $cnd->{Product::P_CATEGORY_ID}       = 0;
            $cnd->{Product::P_SEARCH_IN_SUBCATS} = true;

            try {
                $currentDate = Converter::convertTimeToUser();
                $daysOffset  = abs((int) Config::getInstance()->CDev->ProductAdvisor->na_max_days)
                    ?: Main::PA_MODULE_OPTION_DEFAULT_DAYS_OFFSET;

                $cnd->{\CDev\ProductAdvisor\Model\Repo\Product::P_ARRIVAL_DATE} = array(
                    Converter::getDayStart($currentDate - $daysOffset * 24 * 60 * 60),
                    Converter::getDayEnd($currentDate),
                );
            } catch (\Exception $e) {
            }

            $cnd->{Product::P_ORDER_BY} = [ACustomer::SORT_BY_MODE_DATE, 'desc'];
        }
    }
}
