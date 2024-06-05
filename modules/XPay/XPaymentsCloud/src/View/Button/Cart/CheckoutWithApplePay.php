<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\Cart;

use XCart\Extender\Mapping\ListChild;

/**
 * Checkout With Apple Pay button in Cart
 *
 * @ListChild (list="cart.panel.totals", weight="77")
 */
class CheckoutWithApplePay extends \XPay\XPaymentsCloud\View\Button\ACheckoutWithApplePay
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
     * Returns CSS class for button
     *
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' apple-pay-checkout-button';
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

    /**
     * Returns CSS class for button container
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'apple-pay-checkout-button-container';
    }

    /**
     * Returns JS widget class
     *
     * @return string
     */
    protected function getJSClass()
    {
        return 'XPaymentsCheckoutWithApplePay';
    }

}
