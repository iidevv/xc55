<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\OrderStatus;

/**
 * Payment order status
 */
class Payment extends \XLite\View\OrderStatus\AOrderStatus
{
    /**
     * Return status
     *
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->getOrder()
            ? $this->getOrder()->getPaymentStatus()
            : null;
    }

    /**
     * Return label
     *
     * @return string
     */
    protected function getLabel()
    {
        return '';
    }
}
