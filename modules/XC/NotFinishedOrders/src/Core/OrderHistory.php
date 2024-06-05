<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Core;

use XCart\Extender\Mapping\Extender;

/**
 * OrderHistory
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\Core\OrderHistory
{
    /**
     * Register "Place order" event to the order history
     *
     * @param integer $orderId Order id
     *
     * @return void
     */
    public function registerPlaceOrder($orderId)
    {
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order && !$order->getNotFinishedOrder()) {
            // Do not register 'place order' event for not finished orders
            parent::registerPlaceOrder($orderId);
        }
    }
}
