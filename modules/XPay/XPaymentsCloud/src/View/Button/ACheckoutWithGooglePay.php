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
 * Checkout with Google Pay base button
 */
abstract class ACheckoutWithGooglePay extends \XPay\XPaymentsCloud\View\Button\ACheckoutWithWallet
{
    /**
     * Returns Wallet ID of wallet used for checkout
     *
     * @return string
     */
    protected function getWalletId()
    {
        return 'googlePay';
    }

    /**
     * Return list of required JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_google_pay.js';
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_google_pay_minicart.js';

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
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_google_pay.css';

        return $list;
    }

    /**
     * Returns CSS class
     *
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' google-pay-button';
    }

    /**
     * Returns form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'checkout-with-google-pay-form';
    }

    /**
     * Returns argument for widget_list()
     *
     * @return string
     */
    protected function getWidgetListName()
    {
        return 'xpayments.button.checkoutWithGooglePay';
    }

}
