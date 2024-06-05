<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors;

use XLite\Model\Order\Status\Payment;
use XLite\Model\Order\Status\Shipping;
use XLite\Module\AModule;

abstract class Main extends AModule
{
    /**
     * Default statuses colors
     */
    public const DEFAULT_COLORS = [
        Payment::STATUS_QUEUED => 'fefaee',
        Payment::STATUS_AUTHORIZED => 'fefaee',
        Payment::STATUS_PART_PAID => 'fefaee',

        Payment::STATUS_DECLINED => 'faf0f1',
        Payment::STATUS_CANCELED => 'faf0f1',
        Payment::STATUS_REFUNDED => 'faf0f1',

        Payment::STATUS_PAID . '_' . Shipping::STATUS_NEW => 'eff9f6',
        Payment::STATUS_PAID . '_' . Shipping::STATUS_PROCESSING => 'f6f4f7',

        Payment::STATUS_PAID => 'f0f7ff',
    ];

    /**
     * Return default color of the order status
     *
     * @param string $paymentStatus payment status value
     * @param string $shippingStatus fulfilment status value
     *
     * @return string
     */
    public static function getDefaultColor($paymentStatus, $shippingStatus = null)
    {
        if (isset($shippingStatus)) {
            $shippingStatus = '_' . $shippingStatus;
        }

        return static::DEFAULT_COLORS[trim($paymentStatus) . trim($shippingStatus)]
            ?? static::DEFAULT_COLORS[trim($paymentStatus)]
            ?? '';
    }
}
