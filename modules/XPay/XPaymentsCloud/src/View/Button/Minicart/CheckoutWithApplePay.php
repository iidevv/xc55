<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\Minicart;

use XCart\Extender\Mapping\ListChild;

/**
 * Checkout With Apple Pay button in Minicart
 *
 * @ListChild (list="minicart.horizontal.buttons", weight="77")
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
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' apple-pay-checkout-button apple-pay-minicart';
    }

    /**
     * Returns CSS class for button container
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'apple-pay-checkout-button-minicart-container';
    }

    /**
     * Returns JS widget class
     *
     * @return string
     */
    protected function getJSClass()
    {
        return 'XPaymentsCheckoutWithApplePayMinicart';
    }

}
