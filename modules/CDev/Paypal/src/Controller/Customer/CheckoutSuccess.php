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
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        if (\XLite\Core\Session::getInstance()->inContextRedirect) {
            unset(\XLite\Core\Session::getInstance()->inContextRedirect);
            unset(\XLite\Core\Session::getInstance()->ec_token);
        }

        parent::handleRequest();
    }
}
