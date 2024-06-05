<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OneClickUpsellAfterCheckout\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout success page
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->isAJAXViewer()
            ? null
            : static::t('Thank you for your order');
    }

    /**
     * @return void
     */
    public function handleRequest()
    {
        if ($this->isAJAXViewer()) {
            //skip parent CheckoutSuccess order-orderId statements and redirects for popup
            \XLite\Controller\Customer\ACustomer::handleRequest();
        } else {
            parent::handleRequest();
        }
    }
}
