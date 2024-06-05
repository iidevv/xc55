<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud;

/**
 * Checkout controller
 *
 * @Extender\Mixin
 */
abstract class CheckoutPayment extends \XLite\Controller\Customer\CheckoutPayment implements \XLite\Base\IDecorator
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        if (
            $this->getCart()->getPaymentMethod()
            && $this->getCart()->getPaymentMethod()->getProcessor() instanceof XPaymentsCloud
            || $this->isXpaymentsForcedMode()
        ) {
            $result = '';
        } else {
            $result = parent::getTitle();
        }

        return $result;
    }

    /**
     * We need to override this check to make sure Card Setup will work
     * (even if cart is empty)
     *
     * @return boolean
     */
    public function isCheckoutAvailable()
    {
        if ($this->isXpaymentsForcedMode()) {
            $xpData = \XLite\Core\Session::getInstance()->xpaymentsData;
            if ($xpData && $xpData['xpaymentsBuyWithWallet']) {
                \XLite\Core\Request::getInstance()->xpaymentsWalletId = $xpData['xpaymentsWalletId'];
                \XLite\Core\Request::getInstance()->xpaymentsBuyWithWallet = true;
            }

            $controllerCheckout = new \XLite\Controller\Customer\Checkout();
            $result = $controllerCheckout->isCheckoutAvailable();
        } else {
            $result = parent::isCheckoutAvailable();
        }

        return $result;
    }

    /**
     * Check if page is used for Card Setup or for Buy With Wallet
     *
     * @return bool
     */
    protected function isXpaymentsForcedMode()
    {
        $xpData = \XLite\Core\Session::getInstance()->xpaymentsData;
        return (
            'CardSetup' == \XLite\Core\Request::getInstance()->mode
            || $xpData && $xpData['xpaymentsBuyWithWallet']
        );
    }
}
