<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class OrderStatuses extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'order_statuses';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'order_statuses_payment'  => [
                'weight'     => 100,
                'title'      => static::t('Payment statuses'),
                'url_params' => [
                    'target' => 'order_statuses',
                    'page'   => 'payment',
                ],
                'widget'     => 'XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\Payment',
            ],
            'order_statuses_shipping' => [
                'weight'     => 200,
                'title'      => static::t('Fulfillment statuses'),
                'url_params' => [
                    'target' => 'order_statuses',
                    'page'   => 'shipping',
                ],
                'widget'     => 'XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\Shipping',
            ],
        ];
    }
}
