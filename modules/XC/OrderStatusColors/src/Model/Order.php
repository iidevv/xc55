<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /* Order status color
     *
     * @var string
     */
    protected $statusColor;

    /**
     * Return color code by order statuses
     *
     *
     * @return string
     */
    public function getStatusColor()
    {
        if (!isset($this->statusColor)) {
            $this->statusColor = \XLite\Core\Database::getRepo(OrderStatusColor::class)
                ->getColorByStatuses($this->getPaymentStatus(), $this->getShippingStatus());
        }

        return $this->statusColor;
    }
}
