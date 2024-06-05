<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Payment extends \XLite\Model\Order\Status\Payment
{
    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        $allPossibleStatuses = array_keys($handlers);

        $avataxCancelStatuses = [
            static::STATUS_DECLINED,
            static::STATUS_CANCELED,
            static::STATUS_REFUNDED,
        ];

        foreach ($allPossibleStatuses as $oldStatus) {
            // 1. From any to STATUS_PAID/STATUS_AUTHORIZED/Awaiting Payments
            // see \XC\AvaTax\Model\Order::processSucceed

            // 2. From any to STATUS_CANCELED/STATUS_DECLINED/STATUS_REFUNDED
            foreach ($avataxCancelStatuses as $newStatus) {
                if (in_array($oldStatus, $avataxCancelStatuses)) {
                    continue;
                }
                if (!isset($handlers[$oldStatus][$newStatus])) {
                    $handlers[$oldStatus][$newStatus] = [];
                }

                if (!in_array('avataxVoidTransaction', $handlers[$oldStatus][$newStatus])) {
                    $handlers[$oldStatus][$newStatus][] = 'avataxVoidTransaction';
                }
            }

            // 3. From any* to STATUS_PART_PAID
            // see \XC\AvaTax\Model\Payment\Base\Processor::doTransaction
        }

        return $handlers;
    }
}
