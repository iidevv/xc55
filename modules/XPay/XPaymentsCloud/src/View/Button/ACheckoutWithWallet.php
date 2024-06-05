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
 * Checkout with wallet base button
 */
abstract class ACheckoutWithWallet extends \XLite\View\Button\Regular
{
    /**
     * Returns Wallet ID of wallet used for checkout
     *
     * @return string
     */
    abstract protected function getWalletId();

    /**
     * Returns payment method of wallet used for checkout
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getWalletMethod()
    {
        return XPaymentsHelper::getWalletMethod($this->getWalletId());
    }

    /**
     * Checks if Checkout with wallet button is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getNotEmptyCart();

        return
            parent::isVisible()
            && XPaymentsWallets::isCheckoutWithWalletEnabled($this->getWalletId(), $cart);
    }

    /**
     * Checks current cart and return it only if it is not empty
     *
     * @return \XLite\Model\Cart
     */
    protected function getNotEmptyCart()
    {
        return XPaymentsWallets::getNotEmptyCart($this->getCart());
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/button/checkout_wallet.twig';
    }

    /**
     * Return list of required JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_wallet.js';

        return $list;
    }

    /**
     * Add SDK JS files
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'modules/XPay/XPaymentsCloud/lib/js/widget.js';

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
        $list[] = 'modules/XPay/XPaymentsCloud/button/checkout_wallet.css';

        return $list;
    }

    /**
     * Return X-Payments Cloud payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return XPaymentsHelper::getPaymentMethod();
    }

    /**
     * Returns CSS class
     *
     * @return string
     */
    protected function getButtonClass()
    {
        return 'xpayments-wallet-button';
    }

    /**
     * Returns html tag for button container
     *
     * @return string
     */
    protected function getContainerTag()
    {
        return 'div';
    }

    /**
     * Returns class for button container
     *
     * @return string
     */
    abstract protected function getContainerClass();

    /**
     * Returns form CSS class
     *
     * @return string
     */
    abstract protected function getFormClass();

    /**
     * Returns argument for widget_list()
     *
     * @return string
     */
    abstract protected function getWidgetListName();

    /**
     * Returns JS widget class
     *
     * @return string
     */
    abstract protected function getJSClass();

    /**
     * Checks if X-Payments Cloud method is available in checkout
     *
     * @return bool
     */
    public function isXpaymentsMethodAvailableCheckout()
    {
        static $result = null;

        if (is_null($result)) {
            $result = false;
            if ($this->getCart()) {
                foreach ($this->getCart()->getPaymentMethods() as $method) {
                    if (XPaymentsHelper::XPAYMENTS_SERVICE_NAME == $method->getServiceName()) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns JSON-encoded required address fields list
     *
     * @param string $type Either "shipping" or "billing"
     *
     * @return string
     */
    protected function getRequiredAddressFields($type = 'shipping')
    {
        $result = $this->getWalletMethod()->getProcessor()->getWalletRequiredAddressFields($type, $this->getCart());
        return json_encode($result);
    }

    /**
     * Returns JSON-encoded shipping methods list
     *
     * @return string
     */
    protected function getShippingMethodsList()
    {
        $result = $this->getWalletMethod()->getProcessor()->getWalletShippingMethodsList($this->getCart());
        return json_encode($result);
    }

}
