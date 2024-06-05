<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn
{
    protected $reload = false;

    /**
     * @var \XLite\Model\Payment\Transaction
     */
    protected $transaction;

    /**
     * Set return URL
     *
     * @param string $url URL to set
     *
     * @return void
     */
    public function setReturnURL($url)
    {
        if (
            \CDev\Paypal\Main::isExpressCheckoutEnabled()
            && \XLite\Core\Request::getInstance()->cancelUrl
        ) {
            $url = $this->getShopURL(
                \XLite\Core\Request::getInstance()->cancelUrl,
                \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        parent::setReturnURL($url);
    }
}
