<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button;

use XPay\XPaymentsCloud\Main as XPaymentsHelper;
use \XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

/**
 * Checkout with Apple Pay base button
 */
abstract class ACheckoutWithApplePay extends \XPay\XPaymentsCloud\View\Button\ACheckoutWithWallet
{
    /**
     * Returns Wallet ID of wallet used for checkout
     *
     * @return string
     */
    protected function getWalletId()
    {
        return 'applePay';
    }

    /**
     * Return list of required JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_apple_pay.js';
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_apple_pay_minicart.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_apple_pay.css';

        return $list;
    }

    /**
     * Returns CSS class for button
     *
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' apple-pay-button';
    }

    /**
     * Returns form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'checkout-with-apple-pay-form';
    }

    /**
     * Returns argument for widget_list()
     *
     * @return string
     */
    protected function getWidgetListName()
    {
        return 'xpayments.button.checkoutWithApplePay';
    }

    /**
     * Returns button label for old devices
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Check out with';
    }

    /**
     * It is used to indicate it is Buy or Checkout button
     *
     * @return string
     */
    protected function getButtonMode()
    {
        return 'checkout';
    }

}
