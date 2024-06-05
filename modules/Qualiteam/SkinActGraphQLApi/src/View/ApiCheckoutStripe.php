<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View;

use XC\Stripe\Model\Order;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\Stripe")
 *
 */

class ApiCheckoutStripe extends \Qualiteam\SkinActGraphQLApi\View\ApiCheckout
{
    /**
     * @return array
     */
    protected function getAdditionalPaymentScripts()
    {
        /** @var Order $cart */
        $cart = \XLite::getController()->getCart();
        if ($cart->getPaymentMethod() && $cart->isStripeMethod($cart->getPaymentMethod())) {
            return array_merge(parent::getAdditionalPaymentScripts(), [
                'modules/SkinActGraphQLApi/api_checkout/payment/xc_stripe.js'
            ]);
        }

        return parent::getAdditionalPaymentScripts();
    }
}
