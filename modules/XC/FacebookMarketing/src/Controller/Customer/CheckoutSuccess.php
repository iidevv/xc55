<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout success page
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    /**
     * Print current cart data
     */
    protected function doActionPixelRetrieveOrderData()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);

        $result = [];

        if ($order = $this->getOrder()) {
            $result['order_total'] = $order->getTotal();

            if ($currency = $order->getCurrency() ?: \XLite::getInstance()->getCurrency()) {
                $result['order_currency_code'] = $currency->getCode();
            } else {
                $result['order_currency_code'] = \XLite\View\Model\Currency\Currency::DEFAULT_CURRENCY;
            }
        }

        $this->displayJSON($result);
    }
}
