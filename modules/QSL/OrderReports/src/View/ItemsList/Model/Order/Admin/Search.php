<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\ItemsList\Model\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Search order
 *
 * @autor admin
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/OrderReports/items_list/model/table/order/mobile_order_style.css';

        return $list;
    }

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
