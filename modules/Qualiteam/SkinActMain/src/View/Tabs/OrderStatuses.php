<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderStatuses extends \XC\CustomOrderStatuses\View\Tabs\OrderStatuses
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return array_merge(parent::defineTabs(), [
            'order_statuses_shipping_statuses_bar' => [
                'weight'     => 300,
                'title'      => static::t('Shipping statuses bar'),
                'url_params' => [
                    'target' => 'order_statuses',
                    'page'   => 'shipping_statuses_bar',
                ],
                'widget'     => 'Qualiteam\SkinActMain\View\ItemsList\Model\Order\Status\ShippingBar',
            ]
        ]);
    }
}
