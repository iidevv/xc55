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
class CheckoutFailed extends \XLite\Controller\Customer\CheckoutFailed
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Session::getInstance()->cancelUrl) {
            $this->setReturnURL(\XLite\Core\Session::getInstance()->cancelUrl);

            unset(\XLite\Core\Session::getInstance()->cancelUrl);
        } else {
            parent::doNoAction();
        }
    }
}
