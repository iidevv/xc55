<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Mailer;
use XLite\Model\Order\Status\Payment;

/**
 * @Extender\Mixin
 */
class OrderList extends \XLite\Controller\Admin\OrderList
{
    /**
     * doActionUpdateItemsList
     */
    protected function doActionUpdateItemsList()
    {
        $changes = $this->getOrdersChanges();

        parent::doActionUpdateItemsList();

        foreach ($changes as $orderId => $change) {
            if (!empty($change['paymentStatus']['old']) && !empty($change['paymentStatus']['new'])) {
                $oldCode = $change['paymentStatus']['old'];
                $newPaymentStatus = Database::getRepo('XLite\Model\Order\Status\Payment')
                    ->findOneBy([ 'id' => $change['paymentStatus']['new'] ]);
                $newCode = ($newPaymentStatus ? $newPaymentStatus->getCode() : "");
                if ($newCode && $oldCode !== $newCode && $newCode === Payment::STATUS_PAID) {
                    $order = Database::getRepo('XLite\Model\Order')->findOneBy([ 'order_id' => $orderId ]);
                    if ($order && count($order->getPrivateAttachments()) > 0) {
                        Mailer::sendEgoodsLinks($order);
                    }
                }
            }
        }
    }

    // }}}
}
