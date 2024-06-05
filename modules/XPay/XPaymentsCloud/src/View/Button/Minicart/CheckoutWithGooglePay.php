<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\Minicart;

use XCart\Extender\Mapping\ListChild;

/**
 * Checkout With Google Pay button in Minicart
 *
 * @ListChild (list="minicart.horizontal.buttons", weight="81")
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
        return parent::getButtonClass() . ' google-pay-checkout-button google-pay-minicart';
    }

    /**
     * Returns CSS class for button container
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'google-pay-checkout-button-minicart-container';
    }

    /**
     * Returns JS widget class
     *
     * @return string
     */
    protected function getJSClass()
    {
        return 'XPaymentsCheckoutWithGooglePayMinicart';
    }

}
