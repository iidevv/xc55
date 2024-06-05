<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Checkout canceled page
 */
class CheckoutCanceled extends \XLite\Controller\Customer\Cart
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->setReturnURL($this->buildURL('checkout'));

        \XLite\Core\TopMessage::addError('Sorry, your order payment is canceled.');
    }
}
