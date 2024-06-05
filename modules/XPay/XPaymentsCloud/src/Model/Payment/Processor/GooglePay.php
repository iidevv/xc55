<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Payment\Processor;

use \XPay\XPaymentsCloud\Main as XPaymentsHelper;
use XLite\View\AView;

class GooglePay extends \XPay\XPaymentsCloud\Model\Payment\Processor\AWallet
{
    /**
     * Returns human readable name of current wallet module
     *
     * @return string
     */
    public function getWalletName()
    {
        return 'Google Pay';
    }

    /**
     * Returns classname of current wallet module
     *
     * @return string
     */
    public function getWalletId()
    {
        return 'googlePay';
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
                ->getResourceWebPath('modules/XPay/XPaymentsCloud/google_pay.png');
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
            $option = new \StdClass;
            $option->label = AView::formatPrice($rate->getTotalRate(), $cart->getCurrency()) . ': ' . $this->sanitizeLabel($method->getName());
            $option->description = $method->getProcessor() ? $rate->getPreparedDeliveryTime() : $rate->getDeliveryTime();
            $option->id = strval($method->getMethodId());
            $list[] = $option;
        }

        return $list;
    }

    /**
     * Returns list of required address fields for Google Pay
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
                case 'firstname':
                case 'lastname':
                case 'country_code':
                case 'zipcode':
                    if (!in_array('min', $result)) {
                        $result[] = 'min';
                    }
                    break;
                case 'city':
                case 'state_id':
                case 'street':
                    if (!in_array('full', $result)) {
                        $result[] = 'full';
                    }
                    break;
                case 'phone':
                    $result[] = 'phone';
                    break;
            }
        }

        if (
            !$cart->getProfile()
            || $cart->getProfile()->getAnonymous()
        ) {
            $result[] = 'email';
        }

        $minFound = array_search('min', $result);
        if (false !== $minFound && in_array('full', $result)) {
            unset($result[$minFound]);
            $result = array_values($result);
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
        $result = new \StdClass();

        $result->newTransactionInfo = new \StdClass();
        $result->newTransactionInfo->currencyCode = $cart->getCurrency()->getCode();
        $result->newTransactionInfo->countryCode = \XLite\Core\Config::getInstance()->Company->location_country;
        $result->newTransactionInfo->totalPriceStatus = 'FINAL';
        $result->newTransactionInfo->totalPrice = $cart->getCurrency()->formatValue($cart->getTotal());
        $result->newTransactionInfo->totalPriceLabel = static::t('Total');

        $result->newTransactionInfo->displayItems = [];

        $result->newTransactionInfo->displayItems[] = (object)[
            'label' => static::t('Subtotal'),
            'type' => 'SUBTOTAL',
            'price' => $cart->getCurrency()->formatValue($cart->getDisplaySubtotal())
        ];

        $tax = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_TAX, false);
        if ($cart::ORDER_ZERO < $tax) {
            $result->newTransactionInfo->displayItems[] = (object)[
                'label' => static::t('Tax'),
                'type' => 'TAX',
                'price' => $cart->getCurrency()->formatValue($tax)
            ];
        }

        $discount = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT, false);
        if ($cart::ORDER_ZERO < abs($discount)) {
            $result->newTransactionInfo->displayItems[] = (object)[
                'label' => static::t('Discount'),
                'type' => 'LINE_ITEM',
                'price' => $cart->getCurrency()->formatValue($discount)
            ];
        }

        $shipping = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, false);
        if ($cart::ORDER_ZERO < $shipping) {
            $result->newTransactionInfo->displayItems[] = (object)[
                'label' => static::t('Shipping'),
                'type' => 'LINE_ITEM',
                'price' => $cart->getCurrency()->formatValue($shipping),
            ];
        }
        
        foreach ($result->newTransactionInfo->displayItems as $k => $v) {
            $result->newTransactionInfo->displayItems[$k]->status = 'FINAL';
        }

        return $result;
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
            $newShipping = $this->getWalletShippingMethodsList($cart);
            if ($newShipping) {
                $result->newShippingOptionParameters = new \StdClass;
                $result->newShippingOptionParameters->shippingOptions = $newShipping;
                if ($cart->getShippingId()) {
                    $result->newShippingOptionParameters->defaultSelectedOptionId = strval($cart->getShippingId());
                }
            }
        } else {
            $error = new \StdClass();
            $error->code = 'SHIPPING_ADDRESS_INVALID';
            $error->intent = 'SHIPPING_ADDRESS';
            $error->message = static::t('Shipping address is invalid');

            $result->error = $error;
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
     * Translate array of data received from Google Pay to the array for updating cart
     *
     * @param \XLite\Model\Profile $profile Customer profile
     * @param array $data Array of customer data received from wallet
     *
     * @return array
     */
    public function prepareCheckoutWithWalletContactData($profile, $data)
    {
        if (empty($data['billingAddress'])) {
            $data['billingAddress'] = $data['shippingAddress'];
            $sameAddress = true;
        } else {
            $sameAddress = false;
        }

        $result = [
            'same_address' => $sameAddress,
            'shippingAddress' => $this->convertWalletContactToAddress($data['shippingAddress'], \XLite\Model\Address::SHIPPING, $profile),
            'billingAddress' => $this->convertWalletContactToAddress($data['billingAddress'], \XLite\Model\Address::BILLING, $profile),
        ];

        $email = $data['email'] ?? '';
        $result += $this->prepareCheckoutWithWalletEmail($profile, $email);

        return $result;
    }

    /**
     * Returns list of address errors for Google Pay (if any)
     *
     * @param \XLite\Model\Profile $profile Customer profile
     *
     * @return \StdClass
     */
    protected function checkAddressErrors(\XLite\Model\Profile $profile)
    {
        $error = null;

        foreach ([\XLite\Model\Address::SHIPPING, \XLite\Model\Address::BILLING] as $type) {
            $address = (\XLite\Model\Address::SHIPPING == $type) ? $profile->getShippingAddress() : $profile->getBillingAddress();
            $label = (\XLite\Model\Address::SHIPPING == $type) ? 'Shipping' : 'Billing';

            if (!$address->checkAddress()) {
                $error = (object)[
                    'intent' => 'PAYMENT_AUTHORIZATION',
                    'reason' => 'SHIPPING_ADDRESS_INVALID',
                    'message' => static::t($label . ' address is invalid')
                ];
            } elseif (!$address->isCompleted($type)) {
                $error = (object)[
                    'intent' => 'PAYMENT_AUTHORIZATION',
                    'reason' => 'SHIPPING_ADDRESS_INVALID',
                    'message' => static::t('One or more required address fields are empty')
                ];
            }
        }

        return $error;
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
        if ($cartValid) {
            $result = $this->checkAddressErrors($profile);
        } else {
            $result = (object)[
                'intent' => 'PAYMENT_AUTHORIZATION',
                'reason' => 'SHIPPING_ADDRESS_UNSERVICEABLE',
                'message' => static::t('Failed to process address')
            ];
        }

        return $result;
    }

}
