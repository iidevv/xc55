<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Module\CDev\Paypal\Model\Payment\Processor;

use XCart\Extender\Mapping\Extender;
use XC\NotFinishedOrders\Main;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Paypal")
 */
class ExpressCheckoutMerchantAPI extends \CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI
{
    /**
     * Perform 'SetExpressCheckout' request and get Token value from Paypal
     *
     * @param \XLite\Model\Payment\Method           $method Payment method
     * @param \XLite\Model\Payment\Transaction|null $transaction
     *
     * @return string
     */
    public function doSetExpressCheckout(\XLite\Model\Payment\Method $method, \XLite\Model\Payment\Transaction $transaction = null)
    {
        $result = parent::doSetExpressCheckout($method, $transaction);

        if (Main::isCreateOnPlaceOrder()) {
            $cart = \XLite\Model\Cart::getInstance();
            $cart->processNotFinishedOrder(true);
        }

        return $result;
    }
}
