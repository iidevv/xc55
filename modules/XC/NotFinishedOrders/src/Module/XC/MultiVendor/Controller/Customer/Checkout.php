<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Module\XC\MultiVendor\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout controller extension
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Return true if profile can be cloned
     *
     * @param \XLite\Model\Order $order Order model object
     *
     * @return boolean
     */
    protected function isAllowedCloneProfile($order)
    {
        return parent::isAllowedCloneProfile($order)
            && !($order && $order->isNotFinishedOrder());
    }
}
