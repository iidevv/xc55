<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order list controller
 * @Extender\Mixin
 */
class OrderList extends \XLite\Controller\Admin\OrderList
{
    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        $changes = $this->getOrdersChanges();

        foreach ($changes as $orderId => $change) {
            if (!empty($change['paymentStatus']) || !empty($change['shippingStatus'])) {
                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

                if ($order && $order->isNotFinishedOrder()) {
                    $order->closeNotFinishedOrder();
                }
            }
        }

        parent::doActionUpdateItemsList();
    }
}
