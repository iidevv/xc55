<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Payment\Processor;

use \XPay\XPaymentsCloud\Main as XPaymentsHelper;
use \XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

class ApplePay extends \XPay\XPaymentsCloud\Model\Payment\Processor\AWallet
{

    /**
     * Returns human readable name of current wallet module
     *
     * @return string
     */
    public function getWalletName()
    {
        return 'Apple Pay';
    }

    /**
     * Returns classname of current wallet module
     *
     * @return string
     */
    public function getWalletId()
    {
        return 'applePay';
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return \XLite\Core\Layout::getInstance()
                ->getResourceWebPath('modules/XPay/XPaymentsCloud/apple_pay.png');
    }

    /**
     * Try to verify domain with Apple Pay
     *
     * @return void
     */
    protected function verifyDomain()
    {
        try {

            $result = XPaymentsHelper::getClient()
                ->doVerifyApplePayDomain(XPaymentsHelper::getStorefrontDomain())
                ->result;

        } catch (\XPaymentsCloud\ApiException $exception) {

            $result = false;
        }

        if (!$result) {
            static::$configurationErrors[] = self::ERROR_INVALID_DOMAIN;
        }
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
        $result = [];

        if (
            self::$getWalletsCache->{'applePayPaypal'}
            && self::$getWalletsCache->{'applePayPaypal'}['enabled']
            && static::$getWalletsCache->{'applePayPaypal'}['processorConfigured']
        ) {
            return $result;
        }

        if (!in_array(XPaymentsHelper::getStorefrontDomain(), $walletConfig['domains'])) {
            if (\Includes\Utils\ConfigParser::getOptions(array('service', 'is_cloud'))) {
                $this->verifyDomain();
            } else {
                $result[] = self::ERROR_INVALID_DOMAIN;
            }
        }

        return $result;
    }

    /**
     * Returns parsed list of shipping methods in specified cart
     *
     * @param \XLite\Model\Order $cart Cart
     *
     * @return array
     */
    public function getWalletShippingMethodsList(\XLite\Model\Order $cart)
    {
        $modifier = $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        $list = [];
        foreach ($modifier->getRates() as $rate) {
            /** @var \XLite\Model\Shipping\Rate $rate */
            $method = $rate->getMethod();
            $appleRate = new \StdClass;

            $detail = $method->getProcessor() ? $rate->getPreparedDeliveryTime() : $rate->getDeliveryTime();
            $detail = (string)$detail;

            $appleRate->label = $this->sanitizeLabel($method->getName());
            $appleRate->detail = $detail ?: ' ';
            $appleRate->amount = round($rate->getTotalRate(),2);
            $appleRate->identifier = $method->getMethodId();
            $list[] = $appleRate;
        }

        return $list;
    }

    /**
     * Returns list of required address fields for Apple Pay
     *
     * @param string $type Either "billing" or "shipping"
     * @param \XLite\Model\Order $cart Cart
     *
     * @return array
     */
    public function getWalletRequiredAddressFields($type, \XLite\Model\Order $cart)
    {
        $result = [];
        if ('shipping' == $type) {
            $list = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getShippingRequiredFields();
        } else {
            $list = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getBillingRequiredFields();
        }
        foreach ($list as $field) {
            switch ($field) {
                case 'city':
                case 'country_code':
                case 'state_id':
                case 'street':
                case 'zipcode':
                    if (!in_array('postalAddress', $result)) {
                        $result[] = 'postalAddress';
                    }
                    break;
                case 'phone':
                    $result[] = 'phone';
                    break;
                case 'firstname':
                case 'lastname':
                    if (!in_array('name', $result)) {
                        $result[] = 'name';
                    }
                    break;
            }
        }

        if (
            !$cart->getProfile()
            || $cart->getProfile()->getAnonymous()
        ) {
            $result[] = 'email';
        }

        return $result;
    }

    /**
     * Compose object with cart totals for Checkout with wallet feature
     *
     * @return \StdClass
     */
    protected function getWalletTotals(\XLite\Model\Order $cart)
    {
        $result = array(
            'newTotal' => array(
                'amount' => $cart->getTotal(),
                'type'   => 'final',
            ),
            'newLineItems' => array(
                array(
                    'label'  => static::t('Subtotal'),
                    'amount' => $cart->getDisplaySubtotal(),
                    'type'   => 'final',
                ),
            )
        );

        $tax = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_TAX, false);
        if ($cart::ORDER_ZERO < $tax) {
            $result['newLineItems'][] = array(
                'label'  => static::t('Tax'),
                'amount' => $tax,
                'type'   => 'final',
            );
        }

        $discount = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT, false);
        if ($cart::ORDER_ZERO < abs($discount)) {
            $result['newLineItems'][] = array(
                'label'  => static::t('Discount'),
                'amount' => $discount,
                'type'   => 'final',
            );
        }

        return (object) $result;
    }

    /**
     * Returns specific wallet response data when shipping address is changed during Checkout with wallet
     *
     * @param \XLite\Model\Order $cart
     * @param bool $cartValid
     *
     * @return mixed
     */
    public function handleWalletSetDestination(\XLite\Model\Order $cart, $cartValid)
    {
        $result = $this->getWalletTotals($cart);

        if ($cartValid) {
            $result->newShippingMethods = $this->getWalletShippingMethodsList($cart);

            foreach ($cart->getItems() as $item) {
                if (
                    !$result->newShippingMethods
                    && (
                        !$item->isFreeShipping()
                        || !$item->isShipForFree()
                    )
                ) {
                    $cartValid = false;
                    break;
                }
            }
        }

        if (!$cartValid) {
            $error = new \StdClass();
            $error->code = 'shippingContactInvalid';
            $error->contactField = 'postalAddress';
            $error->message = static::t('Shipping address is invalid');

            $result->errors = [
                $error
            ];
        }

        return $result;
    }

    /**
     * Returns specific wallet response data when shipping method is changed during Checkout with wallet
     *
     * @param \XLite\Model\Order $cart
     * @param bool $cartValid
     *
     * @return mixed
     */
    public function handleWalletChangeMethod(\XLite\Model\Order $cart, $cartValid)
    {
        return $this->getWalletTotals($cart);
    }

    /**
     * Translate array of data received from Apple Pay to the array for updating cart
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param array $data Array of customer data received from wallet
     *
     * @return array
     */
    public function prepareCheckoutWithWalletContactData($profile, $data)
    {
        $result = [
            'same_address' => false,
            'shippingAddress' => $this->convertWalletContactToAddress($data['shippingContact'], \XLite\Model\Address::SHIPPING, $profile),
            'billingAddress' => $this->convertWalletContactToAddress($data['billingContact'], \XLite\Model\Address::BILLING, $profile),
        ];

        $email = $data['shippingContact']['emailAddress'] ?: $data['billingContact']['emailAddress'] ?: '';
        $result += $this->prepareCheckoutWithWalletEmail($profile, $email);

        return $result;
    }

    /**
     * Get missing fields list with Apple Pay codenames
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param string $type Shipping or Billing
     *
     * @return array
     */
    protected function getMissingAddressFields($profile, $type = \XLite\Model\Address::SHIPPING)
    {
        if (\XLite\Model\Address::SHIPPING == $type) {
            $fields = $profile->getShippingAddress()->getRequiredEmptyFields($type);
        } else {
            $fields = $profile->getBillingAddress()->getRequiredEmptyFields($type);
        }

        $errorFields = [];
        foreach ($fields as $field) {
            switch ($field) {
                case 'state':
                case 'state_id':
                case 'custom_state':
                    $errorFields[] = 'administrativeArea';
                    break;
                case 'name':
                    $errorFields[] = 'givenName';
                    $errorFields[] = 'familyName';
                    break;
                case 'street':
                    $errorFields[] = 'addressLines';
                    break;
                case 'country':
                    $errorFields[] = 'countryCode';
                    break;
                case 'city':
                    $errorFields[] = 'locality';
                    break;
                case 'zipcode':
                    $errorFields[] = 'postalCode';
                    break;
                case 'phone':
                    $errorFields[] = 'phoneNumber';
                    break;
            }
        }
        return $errorFields;

    }

    /**
     * Returns list of address errors for Apple Pay (if any)
     *
     * @param \XLite\Model\Profile $profile Customer profile
     *
     * @return array
     */
    protected function checkAddressErrors(\XLite\Model\Profile $profile)
    {
        $errors = [];

        foreach ([\XLite\Model\Address::SHIPPING, \XLite\Model\Address::BILLING] as $type) {
            $address = (\XLite\Model\Address::SHIPPING == $type) ? $profile->getShippingAddress() : $profile->getBillingAddress();
            $label = (\XLite\Model\Address::SHIPPING == $type) ? 'shipping' : 'billing';

            if (!$address->checkAddress()) {
                $errors[] = (object)[
                    'code' => $label . 'ContactInvalid',
                    'contactField' => 'postalAddress',
                    'message' => static::t(ucfirst($label) . ' address is invalid')
                ];
            } elseif (!$address->isCompleted($type)) {
                foreach ($this->getMissingAddressFields($profile, $type) as $appleField) {
                    $errors[] = (object)[
                        'code' => $label . 'ContactInvalid',
                        'contactField' => $appleField,
                        'message' => static::t('One or more required fields are empty')
                    ];
                }
            }
        }

        return $errors;
    }

    /**
     * Handles prepare checkout action before final checkout
     *
     * @param \XLite\Model\Profile $profile
     * @param bool $cartValid
     *
     * @return mixed
     */
    public function handleWalletPrepare(\XLite\Model\Profile $profile, $cartValid)
    {
        $result = new \StdClass();
        $result->errors = [];

        if ($cartValid) {
            $result->errors += $this->checkAddressErrors($profile);
        } else {
            $result->errors[] = (object)[
                'code' => 'addressUnserviceable',
                'contactField' => 'postalAddress',
                'message' => 'Failed to process address'
            ];
        }

        return $result;
    }

}
