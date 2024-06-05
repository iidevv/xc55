<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\Module\XC\CustomOrderStatuses\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class OrderStatuses extends \XC\CustomOrderStatuses\View\Tabs\OrderStatuses
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'order_status_colors';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['order_statuses_order_status_colors'] = [
            'weight'     => 300,
            'title'      => static::t('Order status colors'),
            'url_params' => [
                'target' => 'order_statuses',
                'page'   => 'order_status_colors',
            ],
            'widget'     => 'XC\OrderStatusColors\View\ItemsList\Model\Order\Status\Colors',
        ];

        return $list;
    }
}
