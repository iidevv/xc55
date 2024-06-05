<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Checkout;

use XCart\Extender\Mapping\ListChild;

/**
 * PaypalCredit
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="450")
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="100")
 */
class PaypalCredit extends \CDev\Paypal\View\Button\AExpressCheckout
{
    protected function isVisible()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = $this->getCart();

        return parent::isVisible()
            && \CDev\Paypal\Main::isPaypalCreditEnabled($cart);
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/js/credit.js',
            'modules/CDev/Paypal/button/js/checkout_credit.js'
        ]);
    }

    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pp-style-credit paypal-ec-checkout-credit';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'credit';
    }

    /**
     * @return string
     */
    protected function getButtonLayout()
    {
        return 'horizontal';
    }

    /**
     * @return string
     */
    protected function getButtonColor()
    {
        $configVariable = $this->getButtonStyleNamespace() . '_style_color';

        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$configVariable} ?: 'darkblue';
    }
}
