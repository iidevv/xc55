<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\Model\Repo;

use XLite\Model\Repo\ARepo;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Order\Status\Shipping;
use XC\OrderStatusColors\Main;

/**
 * Order status color repository
 */
class OrderStatusColor extends ARepo
{
    /**
     * Return color code by order statuses
     *
     * @param Payment $paymentStatus
     * @param Shipping $shippingStatus
     *
     * @return string
     */
    public function getColorByStatuses(Payment $paymentStatus = null, Shipping $shippingStatus = null)
    {
        $color = $this->findOneBy([
            'paymentStatus' => $paymentStatus,
            'shippingStatus' => $shippingStatus
        ]);

        if ($color) {
            return trim($color->getColor());
        }

        return Main::getDefaultColor(
            $paymentStatus ? $paymentStatus->getCode() : '',
            $shippingStatus ? $shippingStatus->getCode() : null
        );
    }
}
