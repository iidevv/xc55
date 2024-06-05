<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use QSL\CloudSearch\Model\Repo\Product;
use XCart\Extender\Mapping\Extender;
use XLite;

/**
 * Produces CloudSearch search parameters from CommonCell conditions
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\Backorder"})
 */
class SearchParametersBackorder extends \QSL\CloudSearch\Core\SearchParameters
{
    public static function getStockStatusCondition($condition)
    {
        $cnd = parent::getStockStatusCondition($condition);

        if (!XLite::isAdminZone()) {
            if (in_array(Product::INV_IN, $cnd)) {
                $cnd = array_merge($cnd, [StoreApiBackorder::INV_BACKORDER]);
            }
        }

        return $cnd;
    }
}
