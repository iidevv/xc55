<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\Payment\Processor;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Cart;

/**
 * X-Payments payment processor
 *
 * @Extender\Mixin
 */
abstract class AXPayments extends \Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\AXPayments
{
    /**
     * Get cart
     *
     * @return Cart
     */
    protected function getCart()
    {
        return $this->transaction
            ? $this->transaction->getOrder()
            : Cart::getInstance();
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $this->getCart()->initSubscriptions();

        return parent::doInitialPayment();
    }
}
