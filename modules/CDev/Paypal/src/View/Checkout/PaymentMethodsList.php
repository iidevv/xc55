<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Checkout;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class PaymentMethodsList extends \XLite\View\Checkout\PaymentMethodsList
{
    /**
     * Return list of available payment methods
     *
     * @return array
     */
    protected function getPaymentMethods()
    {
        if (\XLite\Core\Request::getInstance()->ec_returned) {
            return $this->getCart()->getOnlyExpressCheckoutIfAvailable();
        } elseif ($this->isReturnedAfterPaypalCommercePlatform()) {
            return $this->getCart()->getOnlyCommercePlatformIfAvailable();
        }

        return $this->getCart()->getPaymentMethods();
    }
}
