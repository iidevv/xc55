<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\View\Checkout;

use XCart\Extender\Mapping\Extender;
use XC\Stripe\Main;

/**
 * Payment template
 * @Extender\Mixin
 */
abstract class Payment extends \XLite\View\Checkout\Payment
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $isStripeEnabled = Main::getStripeMethod() ? Main::getStripeMethod()->isEnabled() : false;
        $isStripeConnectEnabled = Main::getStripeConnectMethod() ? Main::getStripeConnectMethod()->isEnabled() : false;

        if ($isStripeEnabled || $isStripeConnectEnabled) {
            $list[] = 'modules/XC/Stripe/payment.js';
            $list[] = 'modules/XC/Stripe/place-order.js'; // prevent double submission
            $list[] = ['url' => 'https://checkout.stripe.com/checkout.js'];
            $list[] = ['url' => 'https://js.stripe.com/v3/'];
        }

        return $list;
    }
}
