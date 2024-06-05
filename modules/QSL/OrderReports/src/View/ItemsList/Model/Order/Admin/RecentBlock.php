<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\ItemsList\Model\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Recent orders list block (for dashboard page)
 * @Extender\Mixin
 */
class RecentBlock extends \XLite\View\ItemsList\Model\Order\Admin\RecentBlock
{
    /**
     * Define list columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $result = parent::defineColumns();

        $result['mobileOrder'] = [
            static::COLUMN_NAME     => false,
            static::COLUMN_TEMPLATE => 'modules/QSL/OrderReports/items_list/model/table/order/cell.mobile_order.twig',
            static::COLUMN_ORDERBY  => 90,
        ];

        return $result;
    }
}
