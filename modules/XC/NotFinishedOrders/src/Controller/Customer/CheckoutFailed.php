<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout failed page controller
 * @Extender\Mixin
 */
class CheckoutFailed extends \XLite\Controller\Customer\CheckoutFailed
{
    /**
     * Get failed cart object
     *
     * @return \XLite\Model\Cart
     */
    protected function getFailedCart()
    {
        return $this->getCart()->getNotFinishedOrder() ?: parent::getFailedCart();
    }
}
