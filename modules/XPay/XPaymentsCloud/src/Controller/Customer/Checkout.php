<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use \XPay\XPaymentsCloud\Main as XPaymentsHelper;
use \XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

/**
 * Checkout controller
 *
 * @Extender\Mixin
 */
abstract class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Send event in case if Anonymous customer intended to create profile
     *
     * @throws \Exception
     */
    protected function updateAnonymousProfile()
    {
        parent::updateAnonymousProfile();

        $createProfile = (
            \XLite\Core\Session::getInstance()->order_create_profile
            && \XLite\Core\Session::getInstance()->createProfilePassword
        );

        \XLite\Core\Event::xpaymentsAnonymousRegister(
            array(
                'value' => $createProfile,
            )
        );
    }

    /**
     * Checks if customer is Anonymous and didn't choose to create profile
     * or cart has subscription items
     */
    public function isHideSaveCardCheckbox()
    {
        $anonymous = $this->isAnonymous()
           && (
               !\XLite\Core\Session::getInstance()->order_create_profile
               || !\XLite\Core\Session::getInstance()->createProfilePassword
           );

        $cartHasSubscriptions = $this->getCart()->hasXpaymentsSubscriptions();

        return $anonymous || $cartHasSubscriptions || XPaymentsHelper::isDelayedPaymentEnabled();
    }

    /**
     * Returns X-Payments Cloud payment method
     */
    public function getXpaymentsMethod()
    {
        return XPaymentsHelper::getPaymentMethod();
    }

    /**
     * Returns walletId if current method is Apple Pay/Google Pay/etc or empty string if not
     *
     * @return string
     */
    public function getXpaymentsWalletId()
    {
        return $this->getCart()->getPaymentMethod()->getXpaymentsWalletId();
    }

    /**
     * Returns xpaymentsCustomerId for current profile (if available)
     */
    public function getXpaymentsCustomerId()
    {
        return ($this->getCart()->getProfile())
            ? $this->getCart()->getProfile()->getXpaymentsCustomerId()
            : '';
    }

    /**
     * Sends updated total on cartUpdate (not only the difference)
     *
     * @return boolean
     */
    protected function assembleEvent()
    {
        $result = parent::assembleEvent();
        if ($result) {
            \XLite\Core\Event::xpaymentsTotalUpdate(
                array(
                    'total' => $this->getCart()->getTotal(),
                    'currency' => $this->getCart()->getCurrency()->getCode(),
                )
            );
        }
        return $result;
    }

    /**
     * Get customer profile from session or from cart
     * NOTE: do not uses cache because it is intended to get latest updated datal
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        $profile = $this->getProfile();

        if (!$profile && $this->getCart()) {
            $profile = $this->getCart()->getProfile();
        }

        return $profile;
    }

    /**
     * Delete temporary shipping address
     *
     * @return void
     */
    protected function deleteTemporaryShippingAddress()
    {
        $address = \XLite\Core\Database::getRepo('XLite\Model\Address')->find(
            \XLite\Core\Session::getInstance()->buy_with_wallet_tmp_address_id
        );

        if ($address) {
            $address->delete();
        }
    }

    /**
     * Sets final customer address before proceeding with payment
     *
     * @throws \Exception
     */
    protected function doActionXpaymentsWalletPrepare()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $request = \XLite\Core\Request::getInstance();

        $paymentMethod = XPaymentsHelper::getWalletMethod($request->xpaymentsWalletId);
        if (!$paymentMethod) {
            return;
        }

        $this->getCart()->setPaymentMethod($paymentMethod);

        // Fill the address with data received from Wallet
        $this->requestData = $paymentMethod->getProcessor()->prepareCheckoutWithWalletContactData($this->getCustomerProfile(), $request->getData());

        if (!\XLite\Core\Auth::getInstance()->isLogged()) {
            $this->updateProfile();
        }

        $this->updateShippingAddress();
        $this->updateBillingAddress();

        $this->setCheckoutAvailable();

        $this->updateCart(true);

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        $result = $paymentMethod->getProcessor()->handleWalletPrepare($this->getCustomerProfile(), $this->valid);

        $this->deleteTemporaryShippingAddress();

        echo json_encode($result);
    }

    /**
     * Get customer billing and shipping address for ApplePay
     */
    protected function doActionXpaymentsGetCustomerAdress()
    {
        $profile = $this->getCartProfile();
        $this->silent = true;
        $this->setSuppressOutput(true);
        $this->displayJSON([
            'billingContact'  => $this->convertAddressToApplepayObject($profile->getBillingAddress()),
            'shippingContact' => $this->convertAddressToApplepayObject($profile->getShippingAddress()),
        ]);
    }

    /**
     * Convert address to valid object for ApplePay
     *
     * @param $address
     *
     * @return \StdClass
     */
    protected function convertAddressToApplepayObject($address)
    {
        $obj              = new \StdClass;
        $obj->postalCode  = $address->getZipcode();
        $obj->givenName   = $address->getFirstname();
        $obj->familyName  = $address->getLastname();
        $obj->countryCode = $address->getCountryCode();
        $obj->locality    = $address->getCustomState();
        $obj->country     = $address->getCountry()->getCode3();
        return $obj;
    }

    /**
     * Prepares data and runs default checkout
     *
     * @return void
     */
    protected function doActionXpaymentsWalletCheckout()
    {
        $paymentMethod = XPaymentsHelper::getWalletMethod(\XLite\Core\Request::getInstance()->xpaymentsWalletId);
        if (!$paymentMethod) {
            $this->redirect($this->buildURL('checkout'));
            return;
        }

        $this->getCart()->setPaymentMethod($paymentMethod);

        $this->doActionCheckout();
    }

    /**
     * Fix unsetting of anonymous cart id from session for Buy With Wallet
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithWallet) {
            $savedOrderId = \XLite\Core\Session::getInstance()->order_id;
        }

        parent::processSucceed($fullProcess);

        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithWallet) {
            if (!empty($savedOrderId)) {
                // anonymous cart id was cleared by parent call but it uses
                // unsetBatch() which doesn't update cache and thus
                // we need to "unset" it again first to be able to set it
                unset(\XLite\Core\Session::getInstance()->order_id);
                \XLite\Core\Session::getInstance()->order_id = $savedOrderId;
            }

            // Remove item bought using wallet from real cart
            // if it also contains that item (as in Apple Pay guidelines)
            $item = $this->getCart()->getItems()->first();
            $realCart = \XLite\Model\Cart::getInstance(false);
            $itemInRealCart = $realCart->getItemByItem($item);
            if ($itemInRealCart) {
                $realCart->getItems()->removeElement($itemInRealCart);
                \XLite\Core\Database::getEM()->remove($itemInRealCart);
                \XLite\Core\Database::getEM()->flush();
            }
        }
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
                \XLite\Core\Request::getInstance()->xpaymentsWalletId,
                null !== $doCalculate ? $doCalculate : $this->markCartCalculate()
            );
        } else {
            $cart = parent::getCart($doCalculate);
        }

        return $cart;
    }

    /**
     * Check checkout action accessibility
     *
     * @return boolean
     */
    public function checkCheckoutAction()
    {
        return
            (
                'xpayments_wallet_checkout' == \XLite\Core\Request::getInstance()->action
                && $this->getCart()->getPaymentMethod()
                && $this->getCart()->getPaymentMethod()->isXpayments()
            )
            ? true
            : parent::checkCheckoutAction();
    }

    /**
     * Register anonymous profile if this order has X-Payments subscriptions
     *
     * @param bool $doCloneProfile
     *
     * @return bool
     */
    protected function processCartProfile($doCloneProfile)
    {
        $result = parent::processCartProfile($doCloneProfile);

        if ($this->getCart()->hasXpaymentsSubscriptions()) {
            $this->getCart()->getOrigProfile()->registerAsNew();
        }

        return $result;
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return \XLite\Core\Request::getInstance()->action === 'xpayments_get_customer_adress' ? false : parent::needFormId();
    }

}
