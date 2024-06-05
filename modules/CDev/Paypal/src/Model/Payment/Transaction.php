<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Payment;

use XCart\Extender\Mapping\Extender;
use CDev\Paypal;

/**
 * @Extender\Mixin
 */
class Transaction extends \XLite\Model\Payment\Transaction
{
    /**
     * Check if transaction by Paypal payment method
     *
     * @return boolean
     */
    public function isByPayPal()
    {
        $paymentMethod = $this->getPaymentMethod();

        return in_array($paymentMethod->getServiceName(), Paypal\Main::getServiceCodes(), true);
    }
}
