<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\TwoCheckout\Model\Payment\Processor;

use XLite\InjectLoggerTrait;
use XLite\Model\Base\Surcharge;

/**
 * 2Checkout.com processor
 *
 * Find the latest API document here:
 * https://www.2checkout.com/documentation/Advanced_User_Guide.pdf
 */
class TwoCheckout extends \XLite\Model\Payment\Base\WebBased
{
    use InjectLoggerTrait;

    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        return [
            self::OPERATION_SALE,
        ];
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/CDev/TwoCheckout/config.twig';
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        $this->getLogger('CDev-TwoCheckout')->debug('', [
            'request' => \XLite\Core\Request::getInstance()->getData(),
        ]);

        parent::processReturn($transaction);

        $request = \XLite\Core\Request::getInstance();

        $status = $request->cart_order_id ? $transaction::STATUS_SUCCESS : $transaction::STATUS_FAILED;

        // Checking MD5. /Secret/Account/OrderId/Total cost/
        $orderId = $this->getSetting('mode') === 'test' ? 1 : $request->order_number;

        $calculated = trim($this->getSetting('secret'))
            . trim($this->getSetting('account'))
            . $orderId
            . $request->total;

        $calculated = strtoupper(md5($calculated));

        $this->getLogger('CDev-TwoCheckout')->debug('', [
            'crypt_calculated' => $calculated
        ]);

        if ($calculated != $request->key) {
            $status = $transaction::STATUS_FAILED;

            $this->setDetail('verification', 'MD5 verification failed', 'Verification');

            $this->transaction->setNote('MD5 verification failed');
        }

        // Checking total cost value
        if (!$this->checkTotal($request->total)) {
            $status = $transaction::STATUS_FAILED;
        }

        $this->transaction->setStatus($status);
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $method->getSetting('account')
            && $method->getSetting('secret');
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTML_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return [
            'account',
            'secret',
            'language',
            'mode',
            'prefix',
            'currency',
        ];
    }

    /**
     * Get return request owner transaction or null
     *
     * @return \XLite\Model\Payment\Transaction|void
     */
    public function getReturnOwnerTransaction()
    {
        return \XLite\Core\Request::getInstance()->cart_order_id
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneByPublicTxnId(\XLite\Core\Request::getInstance()->cart_order_id)
            : null;
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
        return true;
    }


    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return 'https://www.2checkout.com/checkout/spurchase';
    }

    /**
     * Format name for request. (firstname + lastname from shipping/billing address)
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getName($address)
    {
        return $address->getFirstname()
            . ' ' . $address->getLastname();
    }

    /**
     * Format state of billing address for request
     *
     * @return string
     */
    protected function getBillingState()
    {
        return $this->getState($this->getProfile()->getBillingAddress());
    }

    /**
     * Format state of shipping address for request
     *
     * @return string
     */
    protected function getShippingState()
    {
        return $this->getState($this->getProfile()->getShippingAddress());
    }

    /**
     * Format state that is provided from $address model for request.
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getState($address)
    {
        $state = $this->getStateFieldValue($address);

        if (empty($state)) {
            $state = 'n/a';
        } elseif (!in_array($this->getCountryField($address), ['US', 'CA'])) {
            $state = 'XX';
        }

        return $state;
    }

    /**
     * Return State field value. If country is US then state code must be used.
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getStateFieldValue($address)
    {
        return $this->getCountryField($address) === 'US'
            ? $address->getState()->getCode()
            : $address->getState()->getState();
    }

    /**
     * Return Country field value. if no country defined we should use '' value
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getCountryField($address)
    {
        return $address->getCountry()
            ? $address->getCountry()->getCode()
            : '';
    }

    /**
     * Return formatted price.
     *
     * @param float $price Price value
     *
     * @return string
     */
    protected function getFormattedPrice($price)
    {
        return sprintf('%.2f', round((double)($price) + 0.00000000001, 2));
    }


    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $fields = [
            'sid'                   => $this->getSetting('account'),
            'total'                 => $this->getFormattedPrice($this->transaction->getValue()),
            'cart_order_id'         => $this->transaction->getPublicTxnId(),
            'merchant_order_id'     => $this->getTransactionId(),
            'pay_method'            => 'CC',
            'lang'                  => $this->getSetting('language'),
            'skip_landing'          => '1',
            'card_holder_name'      => $this->getName($this->getProfile()->getBillingAddress()),
            'street_address'        => $this->getProfile()->getBillingAddress()->getStreet(),
            'city'                  => $this->getProfile()->getBillingAddress()->getCity(),
            'state'                 => $this->getBillingState(),
            'zip'                   => $this->getProfile()->getBillingAddress()->getZipcode(),
            'country'               => $this->getCountryField($this->getProfile()->getBillingAddress()),
            'email'                 => $this->getProfile()->getLogin(),
            'phone'                 => $this->getProfile()->getBillingAddress()->getPhone(),
            'fixed'                 => 'Y',
            'id_type'               => '1',
            'sh_cost'               => $this->getFormattedPrice($this->getOrder()->getSurchargeSumByType(Surcharge::TYPE_SHIPPING)),
        ];

        if ($shippingAddress = $this->getProfile()->getShippingAddress()) {
            $fields += [
                'ship_name'             => $this->getName($shippingAddress),
                'ship_street_address'   => $shippingAddress->getStreet(),
                'ship_city'             => $shippingAddress->getCity(),
                'ship_state'            => $this->getShippingState(),
                'ship_zip'              => $shippingAddress->getZipcode(),
                'ship_country'          => $this->getCountryField($shippingAddress),
            ];
        }

        if ($this->getSetting('mode') === 'test') {
            $fields['demo'] = 'Y';
        }

        $i = -1;

        foreach ($this->getOrder()->getItems() as $item) {
            $product = $item->getProduct();

            $i++;
            $suffix = $i == 0 ? '' : ('_' . $i);

            $description = $product->getCommonDescription() ?: $product->getName();

            $fields['c_prod' . $suffix]        = $product->getProductId() . ',' . $item->getAmount();
            $fields['c_name' . $suffix]        = substr($product->getName(), 0, 127);
            $fields['c_price' . $suffix]       = $this->getFormattedPrice($item->getPrice());
            $fields['c_description' . $suffix] = strip_tags(substr(($description), 0, 254));
        }

        $this->getLogger('CDev-TwoCheckout')->debug('', [
            'fields' => $fields,
        ]);

        return $fields;
    }

    /**
     * Get allowed currencies
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array_merge(
            parent::getAllowedCurrencies($method),
            [$method->getSetting('currency')]
        );
    }
}
