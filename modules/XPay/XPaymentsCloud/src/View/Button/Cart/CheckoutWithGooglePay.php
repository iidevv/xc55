<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\Cart;

use XCart\Extender\Mapping\ListChild;

/**
 * Checkout With Google Pay button in Cart
 *
 * @ListChild (list="cart.panel.totals", weight="81")
 */
class CheckoutWithGooglePay extends \XPay\XPaymentsCloud\View\Button\ACheckoutWithGooglePay
{
    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getNotEmptyCart()
            && $this->isXpaymentsMethodAvailableCheckout();
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' google-pay-checkout-button';
    }

    /**
     * Returns CSS class for button container
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'google-pay-checkout-button-container';
    }

    /**
     * Returns JS widget class
     *
     * @return string
     */
    protected function getJSClass()
    {
        return 'XPaymentsCheckoutWithGooglePay';
    }

    /**
     * Returns html tag for button container
     *
     * @return string
     */
    protected function getContainerTag()
    {
        return 'li';
    }
}
