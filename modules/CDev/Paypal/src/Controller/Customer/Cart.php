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
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * Product status in cart
     *
     * @var boolean
     */
    protected $isAddedSuccessfully = false;

    /**
     * URL to return after product is added
     */
    protected function setURLToReturn()
    {
        if (\XLite\Core\Request::getInstance()->expressCheckout) {
            $params = [
                'cancelUrl' => \XLite\Core\Request::getInstance()->cancelUrl,
            ];

            if (\XLite\Core\Request::getInstance()->inContext) {
                $params['inContext'] = true;
            }

            $url = \XLite::getInstance()->getShopURL(
                $this->buildURL('checkout', 'start_express_checkout', $params),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );

            $this->setReturnURL($url);
        } else {
            parent::setURLToReturn();
        }
    }
}
