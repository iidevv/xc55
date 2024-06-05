<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

use Doctrine\DBAL\LockMode;
use Qualiteam\SkinActXPaymentsConnector\Core\Iframe;
use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Event;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Core\TopMessage;
use XLite\Model\Address;
use XLite\Model\Cart;
use XLite\Model\Order;

/**
 * Checkout
 *
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Used by skin to determine if iframe should be shown
     *
     * @return bool
     */
    public function isCheckoutReady()
    {
        return $this->getCart() 
            && $this->getCart()->getProfile() 
            && $this->getCart()->getProfile()->getLogin()
            && (
                $this->getCart()->getProfile()->getBillingAddress()
                || (
                    $this->getCart()->getProfile()->getShippingAddress()
                    && $this->getCart()->getProfile()->getShippingAddress()->isCompleted(Address::SHIPPING)
                )
            );
    }

    protected function doActionXpcIframe()
    {
        // Enable iframe
        $this->getIframe()->enable();

        $transaction = $this->getCart()->getFirstOpenPaymentTransaction();

        if (
            !$transaction
            || !$transaction->isXpc()
        ) {
            static::sendHeaders();
            // Open transaction was not found or a different processor is used
            $this->getIframe()->setError('');
            $this->getIframe()->setType(Iframe::IFRAME_DO_NOTHING);
            $this->getIframe()->finalize();
        }

        // Actually does redirect to X-Payments payment page
        $this->doActionCheckout();
    }

    /**
     * Show save card checkbox on checkout 
     *
     * @return boolean
     */
    public function showSaveCardBox() 
    {
        $showToUser = (!$this->isAnonymous() && $this->isLogged())
            || Session::getInstance()->order_create_profile;

        $showForPayment = $this->getCart()
            && $this->getCart()->getPaymentMethod()
            && $this->getCart()->getPaymentMethod()->getSetting('saveCards') == 'Y';

        return $showToUser && $showForPayment;
    }

    /**
     * Check if save card checkbox should be added to checkout (API 1.6 and earlier)
     *
     * @return boolean
     */
    public function isOldSaveCardBoxAvailable()
    {
        return (0 > version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.6'));
    } 

    /**
     * Get payment method id
     *
     * @return integer
     */
    public function getPaymentId()
    {
        return ($this->getCart() && $this->getCart()->getPaymentMethod())
            ? $this->getCart()->getPaymentMethod()->getMethodId()
            : 0;
    }

    /**
     * Get X-Payments payment methods ids
     *
     * @return array
     */
    public function getXpcPaymentIds()
    {
        $result = array();

        if ($this->getCart() && $this->getCart()->getPaymentMethods()) {
            foreach ($this->getCart()->getPaymentMethods() as $pm) {
                if ($pm->getClass() == XPayments::class) {
                    $result[] = $pm->getMethodId();
                }
            }
        }

        return $result;
    }

    /**
     * Get X-Payments payment methods id for the saved card payment method
     *
     * @return int
     */
    public function getXpcSavedCardPaymentId()
    {
        $result = 0;

        if ($this->getCart() && $this->getCart()->getPaymentMethods()) {
            foreach ($this->getCart()->getPaymentMethods() as $pm) {
                if ($pm->getClass() == SavedCard::class) {
                    $result = $pm->getMethodId();
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get billing address ID
     *
     * @return int
     */
    public function getXpcBillingAddressId()
    {
        $result = 0;

        if (
            $this->getCart() 
            && $this->getCart()->getProfile()
        ) {

            if ($this->getCart()->getProfile()->getBillingAddress()) {
                $result = $this->getCart()->getProfile()->getBillingAddress()->getAddressId();
            } elseif ($this->getCart()->getProfile()->getShippingAddress()) {
                $result = $this->getCart()->getProfile()->getShippingAddress()->getAddressId();
            }
        }

        return $result;
    }

    /**
     * Is iframe used or not
     *
     * @return bool 
     */
    public function isUseIframe()
    {
        return $this->getIframe()->useIframe();
    }

    /**
     * Save data of the checkout form (notes and flag to save card)
     *
     * @return void
     */
    protected function doActionSaveCheckoutFormData()
    {
        if (Request::getInstance()->notes) {
            $this->getCart()->setNotes(Request::getInstance()->notes);
            Database::getEM()->flush();
        }

        if ($this->isOldSaveCardBoxAvailable()) {
            if ('Y' == Request::getInstance()->save_card) {
                Session::getInstance()->cardSavedAtCheckout = 'Y';
            } else {
                Session::getInstance()->cardSavedAtCheckout = 'N';
            }
        }
    }

    /**
     * Clear init data from session and redirrcet back to checkout
     *
     * @return void
     */
    protected function doActionClearInitData()
    {
        XPaymentsClient::getInstance()->clearInitDataFromSession();

        $this->setHardRedirect();
        $this->setReturnURL($this->buildURL('checkout'));
        $this->doRedirect();
    }

    /**
     * Return from payment gateway
     *
     * @return void
     */
    protected function doActionReturn()
    {
        parent::doActionReturn();

        $orderId = Request::getInstance()->order_id;
        $order = Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order && $order->isXpc()) {

            $order->setPaymentStatusByTransaction($order->getPaymentTransactions()->first());

            Session::getInstance()->selectedCardId = null;

            // Mark card as allowed for further recharges
            // For API 1.6 this flag is set in Model\Payment\Processor\XPayments
            if ($this->isOldSaveCardBoxAvailable()) {
                $useForRecharges = 
                    (
                        'Y' == Request::getInstance()->save_card
                        || 'Y' == Session::getInstance()->cardSavedAtCheckout
                    )
                    ? 'Y' : 'N';

                Session::getInstance()->cardSavedAtCheckout = null;

                foreach ($order->getPaymentTransactions() as $transaction) {
                    if ($transaction->getXpcData()) {
                        $transaction->getXpcData()->setUseForRecharges($useForRecharges);
                    }
                }
            }

            Database::getEM()->flush();
        }
    }

    /**
     * Get class name for save card box 
     *
     * @return string 
     */
    public function getSaveCardBoxClass()
    {
        return Request::getInstance()->xpc_iframe
            ? 'save-card-box'
            : 'save-card-box-no-iframe';

    }

    /**
     * Update profile
     *
     * @return void
     */
    protected function doActionUpdateProfile()
    {
        $beforeUpdateSaveCard = $this->showSaveCardBox();
        $beforeUpdateCheck = $this->checkCheckoutAction();

        parent::doActionUpdateProfile();

        $showSaveCardBox = $this->showSaveCardBox();

        // Reload if anonymous enters his very first address or registers
        $reloadIframe = (
            (!$beforeUpdateCheck && $this->checkCheckoutAction())
            || ($beforeUpdateSaveCard != $showSaveCardBox)
        );

        Event::updateXpcIframe(
            [
                'showSaveCardBox' => $showSaveCardBox,
                'reloadIframe' => $reloadIframe,
            ]
        );
    }

    /**
     * Save selected card id
     *
     * @return void
     */
    protected function doActionSaveSelectedCardId()
    {
        $selectedCardId = Request::getInstance()->selected_card_id;
        if (
            $selectedCardId 
            && $this->getCart()->getProfile()->isCardIdValid($selectedCardId)
        ) {
            Session::getInstance()->selectedCardId = $selectedCardId;
        }
        // To avoid extra AJAX parsing of response
        $this->silent = true;
    }

    /**
     * Get selected card id
     *
     * @return int
     */
    public function getSelectedCardId()
    {
        $selectedCardId = Session::getInstance()->selectedCardId;
        if (
            $selectedCardId 
            && $this->getCart()->getProfile()->isCardIdValid($selectedCardId)
        ) {
            return $selectedCardId;
        }

        return null;

    }

    /**
     * Check is card selected
     *
     * @return bool
     */
    public function isCardSelected($card)
    {
        $selectedCardId = $this->getSelectedCardId();

        return $selectedCardId
            ? ($selectedCardId == $card['card_id'])
            : $card['is_default'];
    }

    /**
     * Set card billing address
     *
     * @return void
     */
    protected function doActionSetCardBillingAddress()
    {
        $addressId = Request::getInstance()->address_id;

        // Get list of Address IDs associated with profile
        $profileAddressIds = array_keys(
            ZeroAuth::getInstance()->getAddressList($this->getCart()->getProfile())
        );        

        if (!in_array($addressId, $profileAddressIds)) {

            // This address is not associated with the customer's profile
            TopMessage::addError('Address not found');

        } else {

            $addresses = $this->getCart()->getProfile()->getAddresses();

            foreach ($addresses as $address) {
                if ($addressId == $address->getAddressId()) {
                    $address->setIsBilling(true);
                } else {
                    $address->setIsBilling(false);
                }
            }

            $shippingAddressId = $this->getCart()->getProfile()->getShippingAddress()->getAddressId();

            $sameAddress = ($addressId == $shippingAddressId);

            Session::getInstance()->same_address = $sameAddress;

            Event::selectCartAddress(
                array(
                    'type'      => Address::BILLING,
                    'addressId' => $addressId,
                    'same'      => $sameAddress,
                )
            );

            Database::getEM()->flush();

            $this->updateCart();

            $this->silenceClose = true; 
        }
    }

    /**
     * Call controller action
     *
     * @return void
     */
    protected function callAction()
    {
        $action = $this->getAction();
        if ($action === 'return') {
            $orderId = Request::getInstance()->order_id;

            Database::getEM()->beginTransaction();

            $cart = Database::getRepo(Cart::class)->find($orderId, LockMode::PESSIMISTIC_WRITE)
                ?: Database::getRepo(Order::class)->find($orderId, LockMode::PESSIMISTIC_WRITE);

            $processAction = true;

            $isXpc = $cart->getPaymentTransactions()->last()->isXpc(true);

            if (
                $isXpc
                && $cart instanceof Cart
            ) {
                $repo = Database::getRepo('XLite\Model\Order');
                $cartData = Database::getEM()->getConnection()->executeQuery(
                    'SELECT * FROM ' . $repo->getTableName() . ' WHERE order_id = ' . $cart->getOrderId()
                )->fetch();

                if ($cartData && $cartData['orderNumber']) {
                    $processAction = false;
                }
            }

            if ($processAction) {
                parent::callAction();

            } else {
                $this->setReturnURL(
                    $this->buildURL(
                        $this->getTarget(),
                        $this->getAction(),
                        ['order_id' => $orderId]
                    )
                );
            }

            Database::getEM()->flush();
            Database::getEM()->commit();

        } else {
            parent::callAction();
        }
    }

    /**
     * Fix SavedCard payment method just in case if marketplace rewrote it
     */
    protected function doNoAction()
    {
        XPaymentsClient::getInstance()->fixSavedCardMethod();

        parent::doNoAction();
    }
}
