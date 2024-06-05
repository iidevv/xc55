<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Checkout;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Payment\Method;

/**
 * Shipping methods list
 *
 * @Extender\Mixin
 */
class PaymentMethodsList extends \XLite\View\Checkout\PaymentMethodsList
{
    /**
     * Check - payment method is selected or not
     *
     * @param Method $method Payment methods
     *
     * @return boolean
     */
    protected function isPaymentSelected(Method $method)
    {
        if (
            $method
            && $method->getClass() == XPayments::class
            && $this->getCart()->getPaymentMethod()
            && $this->getCart()->getPaymentMethod()->getClass() == XPayments::class
        ) {

            $currentMethodServiceName = $this->getCart()->getPaymentMethod()->getServiceName();
            $methodServiceName = $method->getServiceName();

            $result = $methodServiceName === $currentMethodServiceName
                && $method->getSetting('id') == $this->getCart()->getPaymentMethod()->getSetting('id');

        } else {
            $result = parent::isPaymentSelected($method);
        }

        return $result;
    }
}
