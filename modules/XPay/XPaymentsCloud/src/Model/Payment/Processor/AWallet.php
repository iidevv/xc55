<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Payment\Processor;

use \XPay\XPaymentsCloud\Main as XPaymentsHelper;

abstract class AWallet extends \XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud
{
    /**
     * Error codes
     */
    const ERROR_NOT_CONNECTED    = 1;
    const ERROR_WALLETS_DISABLED = 2;
    const ERROR_INVALID_DOMAIN   = 3;
    const ERROR_NO_PAYMENT_CONF  = 4;

    /**
     * List of configuration errors
     *
     * @var array
     */
    protected static $configurationErrors = [];

    /**
     * Get Wallets API call cache
     *
     * @var \XPaymentsCloud\Response
     */
    protected static $getWalletsCache = null;

    /**
     * Flag indicating we need to notify X-Payments about status change
     *
     * @var bool
     */
    protected static $sendStatusChangesByApi = true;

    /**
     * Returns human readable name of current wallet module
     *
     * @return string
     */
    abstract public function getWalletName();

    /**
     * Returns publicId of current wallet module
     *
     * @return string
     */
    abstract public function getWalletId();

    /**
     * Returns classname of current wallet module
     *
     * @return string
     */
    protected function getWalletClass()
    {
        $parts = explode('\\', get_class());
        return end($parts);
    }

    /**
     * Get wallet-specific list of configuration errors to be added to common errors list
     *
     * @param array $walletConfig Config returned for specific wallet in doGetWallets()
     *
     * @return array
     */
    protected function getWalletConfigurationErrors(array $walletConfig)
    {
        return [];
    }

    /**
     * Get list of configuration errors
     *
     * @return array
     */
    protected function getConfigurationErrors()
    {
        $walletId = $this->getWalletId();

        if (array_key_exists($walletId, static::$configurationErrors)) {
            return static::$configurationErrors[$walletId];
        }

        static::$configurationErrors[$walletId] = [];

        $xpMethod = XPaymentsHelper::getPaymentMethod();

        if (
            !$xpMethod
            || !$xpMethod->getProcessor()->isConfigured($xpMethod)
        ) {

            static::$configurationErrors[$walletId][] = self::ERROR_NOT_CONNECTED;

        } elseif (
            !\XLite::getController()->isAJAX()
            && !\XLite\Core\Request::getInstance()->isPost()
            && \XLite::isAdminZone()
        ) {

            try {
                if (is_null(static::$getWalletsCache)) {
                    static::$getWalletsCache = XPaymentsHelper::getClient()->doGetWallets();
                }

                if (!static::$getWalletsCache->walletsEnabled) {
                    static::$configurationErrors[$walletId][] = self::ERROR_WALLETS_DISABLED;
                }

                if (empty(static::$getWalletsCache->{$walletId}['processorConfigured'])) {
                    static::$configurationErrors[$walletId][] = self::ERROR_NO_PAYMENT_CONF;
                }

                $xpaymentsStatus = !empty(static::$getWalletsCache->{$walletId}['enabled']);
                $currentStatus = XPaymentsHelper::getWalletMethod($walletId)->isEnabled();

                if ($xpaymentsStatus != $currentStatus) {
                    static::$sendStatusChangesByApi = false;
                    XPaymentsHelper::getWalletMethod($walletId)->setEnabled($xpaymentsStatus);
                    static::$sendStatusChangesByApi = true;
                    \XLite\Core\Database::getEM()->flush();
                }

                static::$configurationErrors[$walletId] += $this->getWalletConfigurationErrors(static::$getWalletsCache->{$walletId});

                XPaymentsHelper::getWalletMethod($walletId)->setSetting(
                    'configurationErrors',
                    json_encode(static::$configurationErrors[$walletId])
                );

                \XLite\Core\Database::getEM()->flush();

            } catch (\XPaymentsCloud\ApiException $exception) {

                $this->handleApiException($exception, 'Unable to communicate with X-Payments');

                static::$configurationErrors[$walletId] = json_decode(XPaymentsHelper::getWalletMethod($walletId)->getSetting('configurationErrors'), true);
            }

        } else {

            static::$configurationErrors[$walletId] = json_decode(XPaymentsHelper::getWalletMethod($walletId)->getSetting('configurationErrors'), true);
        }

        return static::$configurationErrors[$walletId];
    }

    /**
     * Get translated error message from error code
     *
     * @return string
     */
    protected function getErrorMessage($error)
    {
        $message = '';

        switch ($error) {
            case self::ERROR_NOT_CONNECTED:
                $message = static::t('X-Payments Cloud is not connected');
                break;
            case self::ERROR_WALLETS_DISABLED:
                $message = static::t('X is not available for your X-Payments Cloud account', ['feature' => $this->getWalletName()]);
                break;
            case self::ERROR_INVALID_DOMAIN:
                $message = static::t('Your store domain is not verified by X', ['feature' => $this->getWalletName()]);
                break;
            case self::ERROR_NO_PAYMENT_CONF:
                $message = static::t('No payment processors which support X are enabled in X-Payments Cloud', ['feature' => $this->getWalletName()]);
                break;
            default:
                $message = '';
                break;
        }

        return $message;
    }

    /**
     * Get warning note by payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getWarningNote(\XLite\Model\Payment\Method $method)
    {
        $message = array();

        if (is_array($this->getConfigurationErrors())) {
            foreach ($this->getConfigurationErrors() as $error) {
                $message[] = $this->getErrorMessage($error);
            }
        }

        return !empty($message)
            ? implode(' * ', $message)
            : null;
    }

    /**
     * Payment is configured when required keys set and HTTPS enabled
     *
     * @param \XLite\Model\Payment\Method $method
     *
     * @return bool
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return empty($this->getConfigurationErrors());
    }

    /**
     * Prevent enabling wallets if main method is disabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        $xpMethod = XPaymentsHelper::getPaymentMethod();
        return parent::canEnable($method)
            && $xpMethod
            && $xpMethod->canEnable();
    }

    /**
     * Get payment method row checkout template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/XPay/XPaymentsCloud/checkout/wallet_method.twig';
    }

    /**
     * Send enable wallet request to X-Payments
     *
     * @param bool $enabled
     *
     * @return void
     */
    protected function enableMethodInXpayments($enabled)
    {
        if (
            static::$sendStatusChangesByApi
            && XPaymentsHelper::getClient()
        ) {

            try {

                $result = XPaymentsHelper::getClient()->doSetWalletStatus($this->getWalletId(), $enabled);

            } catch (\XPaymentsCloud\ApiException $exception) {

                $this->handleApiException($exception, 'Unable to communicate with X-Payments');
            }
        }
    }

    /**
     * If wallet has been enabled but for some reason X-Payments Cloud is disabled,
     * then we need to enable that payment method as well
     * Also force enable the wallet in X-Payments too
     *
     * NOTE: We must disable inherited actions here!
     *
     * @return void
     */
    public function enableMethod(\XLite\Model\Payment\Method $method)
    {
        if (
            $method->getEnabled()
            && XPaymentsHelper::getPaymentMethod()
            && !XPaymentsHelper::getPaymentMethod()->getEnabled()
        ) {
            XPaymentsHelper::getPaymentMethod()->setEnabled(true);
        }

        $this->enableMethodInXpayments($method->getEnabled());
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return [
            'configurationErrors',
        ];
    }

    /**
     * Translate array of data received from wallet to the array suitable for X-Cart
     *
     * @param array  $contact Array of customer data received from wallet
     * @param string $type Billing or shipping
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    protected function convertWalletContactToAddress($contact, $type = \XLite\Model\Address::SHIPPING, $profile = null)
    {
        $countryCode = $contact['countryCode'];
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode($countryCode);

        $stateCode = $contact['administrativeArea'];
        $state = ($country && $stateCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($country->getCode(), mb_strtoupper($stateCode, 'UTF-8'))
            : null;

        $data = [
            'country_code' => $countryCode,
            'country' => $country ?: '',
            'state_id' => $state ? $state->getStateId() : null,
            'state' => $state ?: (string)$stateCode,
            'custom_state' => $state ? $state->getState() : (string)$stateCode,
            'city' => $contact['locality'],
            'zipcode' => $contact['postalCode'],
            'phone' => isset($contact['phoneNumber']) ? $contact['phoneNumber'] : '',
        ];

        if (!empty($contact['name'])) {
            // Google Pay
            $data['name'] = $contact['name'];
        } else {
            // Apple Pay
            $data['name'] = $contact['givenName'] . (!empty($contact['familyName']) ? ' ' . $contact['familyName'] : '');
        }

        if (!empty($contact['addressLines'])) {
            // Apple Pay
            $data['street'] = implode(' ', $contact['addressLines']);
        } else {
            // Google Pay
            $data['street'] = $contact['address1'] . ' ' . $contact['address2'] . ' ' . $contact['address3'];
        }

        $func = 'get'  . (\XLite\Model\Address::SHIPPING == $type ? 'Shipping' : 'Billing') . 'Address';

        if (
            $profile
            && $profile->$func()
        ) {
            $data = array_filter($data);
            $data = array_replace(
                $profile->$func()->serialize(),
                $data
            );
        }

        return $data;

    }

    /**
     * Translate email received from wallet to the array for updating cart
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param string $email Received customer e-mail
     *
     * @return array
     */
    protected function prepareCheckoutWithWalletEmail($profile, $email)
    {
        $result = [];

        if (
            !\XLite\Core\Auth::getInstance()->isLogged()
            && !$profile->getLogin()
            && $email
        ) {
            $result += [
                'email'          => $email,
                'create_profile' => false,
            ];
        }

        return $result;
    }

    /**
     * Sanitize label from tags and HTML-entities
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeLabel($value)
    {
        return html_entity_decode(strip_tags($value));
    }

    /**
     * Returns specific wallet response data when shipping address is changed during Checkout with wallet
     *
     * @param \XLite\Model\Order $cart
     * @param bool $cartValid
     *
     * @return mixed
     */
    abstract public function handleWalletSetDestination(\XLite\Model\Order $cart, $cartValid);

    /**
     * Returns specific wallet response data when shipping method is changed during Checkout with wallet
     *
     * @param \XLite\Model\Order $cart
     * @param bool $cartValid
     *
     * @return mixed
     */
    abstract public function handleWalletChangeMethod(\XLite\Model\Order $cart, $cartValid);

    /**
     * Handles prepare checkout action before final checkout
     *
     * @param \XLite\Model\Profile $profile
     * @param bool $cartValid
     *
     * @return mixed
     */
    abstract public function handleWalletPrepare(\XLite\Model\Profile $profile, $cartValid);

    /**
     * Translate array of data received from Apple Pay to the array for updating cart
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param array $data Array of customer data received from wallet
     *
     * @return array
     */
    abstract public function prepareCheckoutWithWalletContactData($profile, $data);

}
