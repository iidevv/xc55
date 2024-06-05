<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Core;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use Qualiteam\SkinActXPaymentsConnector\Transport\Response;
use XLite;
use XLite\Base\Singleton;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Core\TopMessage;
use XLite\Core\Translation;
use XLite\Model\Address;
use XLite\Model\Cart;
use XLite\Model\Payment\Method;
use XLite\Model\Payment\Transaction;
use XLite\Model\Profile;

/**
 * Zero-dollar authorization (card setup)
 *
 */
class ZeroAuth extends Singleton
{
    /**
     * This is a key for the Do not use card setup option
     */
    const DISABLED = -1;

    /**
     * Placeholder for comma in address
     */
    const COMMA = '__COMMA__';

    /**
     * Get config
     *
     * @return object
     */
	protected static function getConfig()
	{
		return Config::getInstance()->Qualiteam->SkinActXPaymentsConnector;
	}

    /**
     * Get X-Payments client 
     *
     * @return object
     */
	protected static function getClient()
	{
		return XPaymentsClient::getInstance();
	}

    /**
     * Get payment method for zero-auth (card setup)
     *
     * @return bool
     */
    public function allowZeroAuth()
    {
        return self::DISABLED != $this->getConfig()->xpc_zero_auth_method_id
            && $this->getPaymentMethod();
    }

    /**
     * Get list of payment methods which allow to save cards and are marked by admin so
     *
     * @param mixed $resultAsTitle Return values as method titles or as objects
     *
     * @return array
     */
    public function getCanSaveCardsMethods($resultAsTitle = false)
    {
        static $list = null; 

        if (is_null($list)) {

            $list = array();

            $paymentMethods = Database::getRepo(Method::class)->findAllActive();

            foreach ($paymentMethods as $pm) {
                if (
                    XPayments::class == $pm->getClass()
                    && 'Y' == $pm->getSetting('saveCards')
                ) {
                    $list[$pm->getMethodId()] = $resultAsTitle ? $pm->getTitle() : $pm;
                }
            }
        }

        return $list;
    }

    /**
     * Get payment method for zero-auth (card setup)
     *
     * @return Method
     */
    public function getPaymentMethod()
    {
        $methods = $this->getCanSaveCardsMethods();

        return array_key_exists($this->getConfig()->xpc_zero_auth_method_id, $methods)
            ? $methods[$this->getConfig()->xpc_zero_auth_method_id]
            : null;
    }

    /**
     * Get customer profile
     *
     * @return Profile
     */
    protected function detectProfile()
    {
        $profile = null;

        if (Request::getInstance()->xpcBackReference) {
            $profile = Database::getRepo(Profile::class)
                ->findOneBy(array('pending_zero_auth' => Request::getInstance()->xpcBackReference));
        }

        return $profile;
    }

    /**
     * Detect payment transaction 
     *
     * @return Transaction
     */
    public function detectTransaction()
    {
        return Request::getInstance()->xpcBackReference
            ? Database::getRepo(Transaction::class)->findOneBy(
                array('public_id' => Request::getInstance()->xpcBackReference)
            )
            : null;
    }

    /**
     * Default description for Card setup
     *
     * @return string
     */
    public static function getDefaultDescription()
    {
        return Translation::lbl('Card setup');
    }

    /**
     * Get address item as string
     *
     * @param Address $address Address
     *
     * @return string
     */
    public function getAddressItem(Address $address)
    {

        $addressFields = $address->getAvailableAddressFields();

        $hasStates = $address->hasStates();

        $result = '';

        foreach ($addressFields as $field) {

            if ('country_code' === $field) {
                $field = 'country';
            }

            if ($hasStates) {
                if ('state_id' === $field) {
                    $field = 'state';
                } elseif ('custom_state' === $field) {
                    continue;
                }
            } else {
                if ('state_id' === $field) {
                    continue;
                }
            }

            $method = 'get' . ucfirst($field);

            $item = $address->$method();

            if (is_callable(array($item, $method))) {
                $item = $item->$method();
            }

            $result = $result . ' ' . $item;
        }

        return trim($result);
    }

    /**
     * Get list of addresses
     *
     * @param Profile $profile Customer's profile
     *
     * @return array 
     */
    public function getAddressList(Profile $profile)
    {
        static $list = array();

        if (empty($list)) {
            $addresses = $profile->getAddresses()->toArray();

            foreach ($addresses as $address) {
                $list[$address->getAddressId()] = $this->getAddressItem($address);
            }
        }

        return $list;
    }

    /**
     * Is it single address or there are some more
     *
     * @param Profile $profile Customer's profile
     *
     * @return bool 
     */
    public function isSingleAddress(Profile $profile)
    {
        return 1 == count($this->getAddressList($profile));
    }

    /**
     * Get string line for the single address 
     *
     * @param Profile $profile Customer's profile
     *
     * @return string 
     */
    public function getSingleAddress(Profile $profile)
    {
        $list = $this->getAddressList($profile);

        return array_shift($list);
    }

    /**
     * Get address ID
     *
     * @param Profile $profile Customer's profile
     *
     * return int
     */
    public function getAddressId(Profile $profile)
    {
        if ($profile->getBillingAddress()) {
            $addressId = $profile->getBillingAddress()->getAddressId();
        } else {
            $list = $this->getAddressList($profile);
            $addressId = key($list);
        }

        return $addressId;
    }

    /**
     * Create cart
     *
     * @param Profile $profile Customer's profile
     *
     * @return Cart
     */
    protected function createCart(Profile $profile)
    {
        $cart = $this->getClient()->createFakeCart(
            $profile,
            $this->getPaymentMethod(),
            $this->getConfig()->xpc_zero_auth_amount,
            $this->getConfig()->xpc_zero_auth_description
                ? $this->getConfig()->xpc_zero_auth_description
                : self::getDefaultDescription(),
            'CardSetup',
            $this->getAddressId($profile)
        );

        return $cart;
    }

    /**
     * Prepare cart hash to send to X-Payments
     *
     * @param Cart $cart Customers cart
     *
     * @return array
     */
    protected function getPreparedCart(Cart $cart)
    {
        return $this->getClient()->prepareCart($cart, $this->getPaymentMethod(), null, true, true);
    }

    /**
     * Get iframe form fields to post
     *
     * @param Transaction $transaction Transaction for fake cart
     * @param array $preparedCart Prepared cart as array for API request
     * @param string $xpcBackReference X-Cart transaction reference
     *
     * @return Response
     */
    protected function getInitDataRequest(Transaction $transaction, array $preparedCart, $xpcBackReference)
    {
        // Data to send to X-Payments
        $data = array(
            'confId'      => intval($this->getPaymentMethod()->getSetting('id')),
            'refId'       => $xpcBackReference,
            'cart'        => $preparedCart,
            'language'    => Session::getInstance()->getLanguage()->getCode(),
            'returnUrl'   => $this->getClient()->getReturnUrl($xpcBackReference, true),
            'callbackUrl' => $this->getClient()->getCallbackUrl($xpcBackReference),
        );

        // For API v1.3-v1.6 we need to force the template for iframe
        if (
            version_compare($this->getConfig()->xpc_api_version, '1.3') >= 0
            && version_compare($this->getConfig()->xpc_api_version, '1.6') < 0
        ) {

            $data += array(
                'saveCard'    => 'Y',
                'template'    => 'xc5',
            );
        }

        $request = $this->getClient()->getApiRequest()->send(
            'payment',
            'init',
            $data
        );

        if ($request->isSuccess()) {

            $response = $request->getResponse();

            // Set fields for the "Redirect to X-Payments" form
            $data = [ 
                'xpcBackReference' => $xpcBackReference,
                'txnId'            => $response['txnId'],
                'module_name'      => $this->getPaymentMethod()->getSetting('moduleName'),
                'url'              => Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_xpayments_url . '/payment.php',
                'expiryTime'       => Converter::time() + 900,
                'fields'           => array(
                    'target' => 'main',
                    'action' => 'start',
                    'token'  => $response['token'],
                ),
            ];
            $request->setResponse($data);

            $this->getClient()->saveInitDataToSession($transaction, $data);

        }

        return $request;
    }

    /**
     * JS code to redirect back to saved cards page
     *
     * @param Profile $profile Customer's profile
     *
     * @return string 
     */
    protected function getRedirectCode(Profile $profile)
    {
        $url = XLite::getInstance()->getShopUrl(
                Converter::buildUrl(
                    'saved_cards', 
                    '', 
                    array('profile_id' => $profile->getProfileId()),
                    $profile->getPendingZeroAuthInterface()
                )
            );

        return '<script type="text/javascript">'
			. 'window.parent.location = "' . $url . '";'
			. '</script>';
    }

    /**
     * Cleanup pending zero-auth data from profile 
     *
     * @param Profile $profile Customer's profile
     *
     * @return void
     */
    protected function cleanupZeroAuthPendingData(Profile $profile)
    {
        $profile->setPendingZeroAuthTxnId('');
        $profile->setPendingZeroAuth('');
        $profile->setPendingZeroAuthInterface('');

        Database::getEM()->flush();
    }

    /**
     * Display form inside iframe that redirects to X-Payments
     *
     * @param Profile $profile Customer's profile
     * @param string $interface Admin or Customer interface
     *
     * @return void
     */
    public function doActionXpcIframe(Profile $profile, $interface = false)
    {
        if (!$interface) {
            $interface = XLite::getCustomerScript();
        }

        // Cleanup fake carts from session
        self::cleanupFakeCartsForProfile($profile);

        // Prepare cart
        $cart = $this->createCart($profile);
        $preparedCart = $this->getPreparedCart($cart);

        if ($preparedCart) {

            $transaction = $cart->getFirstOpenPaymentTransaction();

            $this->getPaymentMethod()->getProcessor()->savePaymentSettingsToTransaction($transaction);

            $xpcBackReference = $transaction->getPublicId();

            $profile->setPendingZeroAuth($xpcBackReference);
            $profile->setPendingZeroAuthInterface($interface);
            Database::getEM()->flush();

            $request = $this->getInitDataRequest($transaction, $preparedCart, $xpcBackReference);

            if ($request->isSuccess()) {

                $data = $request->getResponse();

                $transaction->setDataCell('xpc_txnid', $data['txnId'], 'X-Payments transaction id', 'C');
                $transaction->setDataCell('xpcBackReference', $xpcBackReference, 'X-Payments back reference', 'C');
                // Set flag to deny callbacks until final processing
                $transaction->setXpcDataCell('xpc_deny_callbacks', '1');

                $transaction->setXpcDataCell('xpc_session_id', Session::getInstance()->getID());

                // AntiFraud service
                if (method_exists($transaction, 'processAntiFraudCheck')) {
                    $transaction->processAntiFraudCheck();
                }

                Database::getEM()->flush();

                $this->getPaymentMethod()->getProcessor()->pay($transaction);

            } else {

                // Parse error
                $message = $request->getError();

                $transaction->setDataCell('status', $message, 'X-Payments error', 'C');
                $transaction->setNote($message);

                $iframe = Iframe::getInstance();

                $iframe->setError($message);
                $iframe->setType(Iframe::IFRAME_ALERT);

                $iframe->finalize();

            }

        }
    }

    /**
     * Return action
     *
     * @return void
     */
    public function doActionReturn()
    {
        $profile = $this->detectProfile();
        $transaction = $this->detectTransaction();

        if (
            $profile
            && $transaction
        ) {

            $transaction->getPaymentMethod()->getProcessor()->processReturn($transaction);

            $cart = $transaction->getOrder();
            if ($cart instanceof Cart) {
                $cart->tryClose();
            }
            $transaction->getOrder()->setPaymentStatusByTransaction($transaction);
            $transaction->getOrder()->update();

            $isOldApi = (version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.6') < 0);

            if (
                $transaction->isCompleted()
                && (
                    $isOldApi
                    || 'Y' == $transaction->getXpcData()->getUseForRecharges()
                )
            ) {

                if ($isOldApi) {
                    $transaction->getXpcData()->setUseForRecharges('Y');
                }

                TopMessage::addInfo('Card has been successfully saved');

            } else {

                TopMessage::addError('Card was not saved due to payment processor error');
            }

            Database::getEM()->flush();

            echo $this->getRedirectCode($profile);

            // Cleanup pending zero-auth data
            $this->cleanupZeroAuthPendingData($profile);

            // Cleanup fake carts from session
            self::cleanupFakeCartsForProfile($profile);

            exit;


        } else {

            die('Error occured when saving card. Customer profile not found');
            // Just in case show error inside iframe. However this should not happen

        }

	}

    /**
     * Update address (set selected address for the current zero auth)
     *
     * @param Profile $profile Customer's profile
     *
     * return void
     */
    public function doActionUpdateAddress(Profile $profile)
    {
        $addresses = $profile->getAddresses();

        foreach ($addresses as $address) {
            if (Request::getInstance()->address_id == $address->getAddressId()) {
                $address->setIsBilling(true);
            } else {
                $address->setIsBilling(false);
            }
        }

        Database::getEM()->flush();
    }

    /**
     * Mark fake cart as order
     *
     * @param Profile $profile Customer's profile
     *
     * @return void
     */
    public function processSucceedFakeCart(Profile $profile)
    {
        $carts = Database::getRepo(Cart::class)->findByProfile($profile);

        if ($carts) {
            foreach ($carts as $cart) {

                // Fake cart contains only one item, but there is no first() method
                $item = $cart->getItems()->last();

                if (
                    $item
                    && $item->isXpcFakeItem()
                    && $cart->getPaymentStatus()
                ) {
                    // Reset profile in cart to avoid removing it on cascade
                    $cart->setProfileCopy($profile);
                    $cart->processSucceed();

                }

            }

            Database::getEM()->flush();
        }
    }
    /**
     * Cleanup fake carts from session
     *
     * @param Profile $profile Customer's profile
     *
     * @return void
     */
    public static function cleanupFakeCartsForProfile(Profile $profile)
    {
        $carts = Database::getRepo(Cart::class)->findByProfile($profile);

        if ($carts) {
            self::cleanupFakeCarts($carts, true);
        }
    }

    /**
     * Cleanup fake carts from session
     *
     * @param array $carts List of carts
     * @param bool $flush Flush or not
     *
     * @return void
     */
    public static function cleanupFakeCarts($carts = array(), $flush = false)
    {
        if (empty($carts)) {
            $carts = Database::getRepo(Cart::class)->findBy(['is_zero_auth' => true]);
            if (!$carts) {
                $carts = array();
            }
        }

        foreach ($carts as $cart) {

            // Do not remove paid cart
            if ($cart->getPaymentStatus()) {
                continue;
            }

            // Fake cart contains only one item, but there is no first() method
            $item = $cart->getItems()->last();

            if (
                $item
                && $item->isXpcFakeItem()
            ) {
                // Reset profile in cart to avoid removing it on cascade
                $cart->setProfile(null);
                Database::getEM()->remove($cart);
            }

        }

        if ($flush) {
            Database::getEM()->flush();
        }
    }
}
