<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Customer;

use \XPay\XPaymentsCloud\Main as XPaymentsHelper;
use \XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

/**
 * Shipping estimator
 */
class WalletShipping extends \XLite\Controller\Customer\ShippingEstimate
{

    /**
     * Detect state by code and adjust it to the request
     *
     * @return void
     */
    protected function detectState()
    {
        $customState = strval(\XLite\Core\Request::getInstance()->destination_custom_state);
        $country = strval(\XLite\Core\Request::getInstance()->destination_country);

        $state = \XLite\Core\Database::getRepo('XLite\Model\State')
            ->findOneByCountryAndState($country, $customState);

        if ($state && $state->getStateId()) {
            \XLite\Core\Request::getInstance()->destination_state = $state->getStateId();
        }
    }

    /**
     * handle cancelled action
     *
     * @return void
     */
    protected function doActionCancelled()
    {
        $origAddress = \XLite\Core\Database::getRepo('XLite\Model\Address')->find(
            \XLite\Core\Session::getInstance()->buy_with_wallet_orig_address_id
        );

        $address = \XLite\Core\Database::getRepo('XLite\Model\Address')->find(
            \XLite\Core\Session::getInstance()->buy_with_wallet_tmp_address_id
        );

        if ($origAddress && $address) {
            $this->getCartProfile()->setShippingAddress($origAddress);
            $address->delete();
        }
    }

    /**
     * Set estimate destination
     *
     * @return void
     */
    protected function doActionSetDestination()
    {
        $method = $this->getWalletMethod();
        $this->getCart()->setPaymentMethod($method);

        $this->detectState();

        $origAddress = $this->getCartProfile()->getShippingAddress();
        if ($origAddress) {
            $address = $origAddress->cloneEntity();
            \XLite\Core\Database::getEM()->persist($address);
            $this->getCartProfile()->setShippingAddress($address);

            \XLite\Core\Session::getInstance()->buy_with_wallet_orig_address_id = $origAddress->getAddressId();
            \XLite\Core\Session::getInstance()->buy_with_wallet_tmp_address_id = $address->getAddressId();
        }

        parent::doActionSetDestination();

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        $result = $method->getProcessor()->handleWalletSetDestination($this->getCart(), $this->valid);

        echo json_encode($result);
    }

    /**
     * Change shipping method
     *
     * @return void
     */
    protected function doActionChangeMethod()
    {
        $method = $this->getWalletMethod();
        $this->getCart()->setPaymentMethod($method);

        parent::doActionChangeMethod();

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        $this->updateCart();

        $result = $method->getProcessor()->handleWalletChangeMethod($this->getCart(), $this->valid);

        echo json_encode($result);
    }

    /**
     * Force silent flag to avoid reloads when shipping selected
     *
     * @param boolean $silent
     *
     * @throws \Exception
     */
    protected function updateCart($silent = false)
    {
        parent::updateCart(true);
    }

    /**
     * Returns Wallet ID that requested estimation
     *
     * @return string
     */
    protected function getWalletId()
    {
        $walletId = \XLite\Core\Request::getInstance()->xpaymentsWalletId;
        return $walletId;
    }

    /**
     * Returns Wallet payment method that requested estimation
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getWalletMethod()
    {
        return XPaymentsHelper::getWalletMethod($this->getWalletId());
    }

    /**
     * Return cart instance or Buy With Wallet Cart
     *
     * @param null|boolean $doCalculate Flag: completely recalculate cart if true OPTIONAL
     *
     * @return \XLite\Model\Order
     */
    public function getCart($doCalculate = null)
    {
        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithWallet) {
            $cart = XPaymentsWallets::getBuyWithWalletCart(
                $this->getWalletId(),
                null !== $doCalculate ? $doCalculate : $this->markCartCalculate()
            );
        } else {
            $cart = parent::getCart($doCalculate);
        }

        return $cart;
    }

}
