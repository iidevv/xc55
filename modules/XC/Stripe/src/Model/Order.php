<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model;

use XCart\Extender\Mapping\Extender;
use XC\Stripe\Main;

/**
 * Order model
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Called when an order successfully placed by a client
     */
    public function processSucceed()
    {
        parent::processSucceed();

        if ($this->isStripeMethod($this->getPaymentMethod())) {
            // Unlock IPN processing for each transaction
            foreach ($this->getPaymentTransactions() as $transaction) {
                $transaction->unsetEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
            }
        }
    }

    /**
     * Checks if order payment method is Stripe
     *
     * @param \XLite\Model\Payment\Method $method
     *
     * @return bool
     */
    public function isStripeMethod($method)
    {
        return $method !== null
            && in_array($method->getServiceName(), [Main::STRIPE_SERVICE_NAME, Main::STRIPE_CONNECT_SERVICE_NAME], true);
    }
}
