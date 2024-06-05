<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use XCart\Extender\Mapping\Extender;
use CDev\Paypal\Model\Payment\Processor\ExpressCheckout as ExpressCheckoutProcessor;

/**
 * @Extender\Mixin
 */
class CheckoutReview extends \XLite\View\Checkout\Step\Review
{
    /**
     * Return false if Express Checkout shortcut is selected by customer
     *
     * @return boolean
     */
    protected function isNeedReplaceLabel()
    {
        $result = parent::isNeedReplaceLabel();

        if ($result) {
            $cart = $this->getCart();

            if (
                $cart->isExpressCheckout($cart->getPaymentMethod())
                && \XLite\Core\Session::getInstance()->ec_type == ExpressCheckoutProcessor::EC_TYPE_SHORTCUT
            ) {
                $result = false;
            }
        }

        return $result;
    }
}
