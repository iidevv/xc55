<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Order;
use XLite\Model\OrderHistoryEvents;

/**
 * Order history main point of execution
 *
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\Core\OrderHistory
{
    /**
     * Texts for the order history event descriptions
     */
    const TXT_PLACE_ORDER_UPDATED           = 'Order updated';

    /**
     * Text for place order description
     *
     * @param integer $orderId Order id
     *
     * @return string
     */
    protected function getPlaceOrderDescription($orderId)
    {
        $alreadyPlaced = false;

        $order = Database::getRepo(Order::class)->find($orderId);

        if ($order) {
            foreach (Database::getRepo(OrderHistoryEvents::class)->findAllByOrder($order) as $event) {
                if (static::CODE_PLACE_ORDER == $event->getCode()) {
                    $alreadyPlaced = true;
                    break;
                }
            }
        }

        return $alreadyPlaced
            ? static::TXT_PLACE_ORDER_UPDATED
            : static::TXT_PLACE_ORDER;
    }
}
