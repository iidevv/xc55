<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpMissingReturnTypeInspection */

namespace CDev\GoogleAnalytics\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Order payment status
 *
 * @Extender\Mixin
 */
abstract class Payment extends \XLite\Model\Order\Status\Payment
{
    /**
     * Return status handlers list
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        foreach (static::getGANotPaidStatuses() as $status) {
            if (!isset($handlers[$status])) {
                $handlers[$status] = [
                    static::STATUS_PAID => [],
                ];
            }
            if (!isset($handlers[$status][static::STATUS_PAID])) {
                $handlers[$status][static::STATUS_PAID] = [];
            }

            // From NOTPAID to PAID state change
            $handlers[$status][static::STATUS_PAID][] = 'registerGAPurchase';

            if (!isset($handlers[static::STATUS_PAID][$status])) {
                $handlers[static::STATUS_PAID][$status] = [];
            }
            // From PAID to NOTPAID state change
            $handlers[static::STATUS_PAID][$status][] = 'registerGARefund';

            if ($status !== static::STATUS_QUEUED) {
                if (!isset($handlers[static::STATUS_QUEUED][$status])) {
                    $handlers[static::STATUS_QUEUED][$status] = [];
                }
                // From STATUS_QUEUED to NOTPAID state change
                $handlers[static::STATUS_QUEUED][$status][] = 'registerGARefundFromQueued';
            }
        }

        return $handlers;
    }

    /**
     * Get open order statuses
     *
     * @return array
     */
    public static function getGANotPaidStatuses()
    {
        return [
            static::STATUS_QUEUED,
            static::STATUS_REFUNDED,
            static::STATUS_DECLINED,
            static::STATUS_CANCELED,
            static::STATUS_AUTHORIZED,
        ];
    }
}
