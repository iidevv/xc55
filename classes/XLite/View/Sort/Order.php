<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Sort;

use XCart\Extender\Mapping\ListChild;

/**
 * Order sort widget
 *
 * @ListChild (list="orders.panel", weight="20")
 */
class Order extends \XLite\View\Sort\ASort
{
    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_PARAMS]->setValue(
            [
                'target' => 'order_list',
                'mode' => 'search',
            ]
        );

        $this->widgetParams[self::PARAM_SORT_CRITERIA]->setValue(
            [
                'order_id' => 'Order id',
                'date'     => 'Date',
                'status'   => 'Status',
                'total'    => 'Total',
            ]
        );

        $this->widgetParams[self::PARAM_CELL]->setValue(\XLite\Core\Session::getInstance()->orders_search);
    }
}
