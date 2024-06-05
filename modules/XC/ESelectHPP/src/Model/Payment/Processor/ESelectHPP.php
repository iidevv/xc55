<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ESelectHPP\Model\Payment\Processor;

use XLite\InjectLoggerTrait;
use XLite\Model\Base\Surcharge;

/**
 * ESelectHPP processor
 *
 */
class ESelectHPP extends \XLite\Model\Payment\Base\WebBased
{
    use InjectLoggerTrait;

    /**
     * Card types
     *
     * @var array
     */
    protected $cardTypes = [
        'M'    => 'MasterCard',
        'V'    => 'Visa',
        'AX'   => "American Express",
        'DC'   => "Diners Card",
        'NO'   => "Novus / Discover",
        'SE'   => 'Sear',
        'null' => 'Unknown',
    ];

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
        return '\XC\ESelectHPP\View\Config';
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
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        $request = \XLite\Core\Request::getInstance();

        $this->getLogger('XC-ESelectHPP')->debug('', [
            'return' => $request->getData(),
        ]);

        $status = $transaction::STATUS_FAILED;

        if (!empty($request->response_order_id) && !empty($request->response_code)) {
            if (
                $request->response_code != 'null'
                && 50 > $request->response_code
                && $request->result == 1
                && $this->checkTotal($request->charge_total)
            ) {
                $status = $transaction::STATUS_SUCCESS;
            } elseif ($request->message && $request->message != 'null') {
                $transaction->setNote($request->message);
                $this->setDetail(
                    'status',
                    $request->message,
                    'Fail reason'
                );
            }
        }

        $transaction->setStatus($status);

        if ($request->bank_transaction_id != 'null') {
            $this->setDetail(
                'BankTransID',
                $request->bank_transaction_id,
                'Bank transaction ID'
            );
        }

        if ($request->bank_approval_code != 'null') {
            $this->setDetail(
                'BankApproval',
                $request->bank_approval_code,
                'Bank approval code'
            );
        }

        if (!empty($request->transactionKey)) {
            $this->setDetail(
                'TransactionKey',
                $request->transactionKey,
                'Transaction key'
            );
        }

        if (!empty($request->txn_num)) {
            $this->setDetail('TxnNum', $request->txn_num, 'Transaction number');
        }

        if (!empty($request->f4l4)) {
            $this->setDetail(
                'CardF4L4',
                $this->cardTypes[$request->card] . ' #' . $request->f4l4 . ', Exp.:' . $request->expiry_date,
                'Card'
            );
        }

        if ($request->trans_name && $request->trans_name != 'null') {
            $this->setDetail(
                'TransName',
                $request->trans_name,
                'Transaction type'
            );
        }
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
            && $method->getSetting('store_id')
            && $method->getSetting('hpp_key');
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return [
            'store_id',
            'hpp_key',
            'language',
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
        return \XLite\Core\Request::getInstance()->rvar_txnid
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneByPublicTxnId(\XLite\Core\Request::getInstance()->rvar_txnid)
            : null;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->getSetting('mode') === 'test'
            ? 'https://esqa.moneris.com/HPPDP/index.php'
            : 'https://www3.moneris.com/HPPDP/index.php';
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
        return number_format(
            $this->transaction->getCurrency()->roundValue($price),
            2,
            '.',
            ''
        );
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $fields = [
            'ps_store_id'               => $this->getSetting('store_id'),
            'hpp_key'                   => $this->getSetting('hpp_key'),
            'charge_total'              => $this->getFormattedPrice($this->transaction->getValue()),
            'order_id'                  => $this->getTransactionId(),

            'cust_id'                   => $this->getProfile()->getLogin(),
            'email'                     => $this->getProfile()->getLogin(),
            'shipping_cost'             => $this->getFormattedPrice($this->getOrder()->getSurchargeSumByType(Surcharge::TYPE_SHIPPING)),

            'bill_first_name'           => $this->getProfile()->getBillingAddress()->getFirstName(),
            'bill_last_name'            => $this->getProfile()->getBillingAddress()->getLastName(),
            'bill_address_one'          => $this->getProfile()->getBillingAddress()->getStreet(),
            'bill_city'                 => $this->getProfile()->getBillingAddress()->getCity(),
            'bill_state_or_province'    => $this->getBillingState(),
            'bill_postal_code'          => $this->getProfile()->getBillingAddress()->getZipcode(),
            'bill_country'              => $this->getCountryField($this->getProfile()->getBillingAddress()),
            'bill_phone'                => $this->getProfile()->getBillingAddress()->getPhone(),

            'rvar_txnid'                => $this->transaction->getPublicTxnId(),
        ];
        $shippingAddress = $this->getProfile()->getShippingAddress();
        if ($shippingAddress) {
            $fields += [
                'ship_first_name'        => $shippingAddress->getFirstName(),
                'ship_last_name'         => $shippingAddress->getLastName(),
                'ship_company_name'      => \XLite\Core\Config::getInstance()->Company->company_name,
                'ship_address_one'       => $shippingAddress->getStreet(),
                'ship_city'              => $shippingAddress->getCity(),
                'ship_state_or_province' => $this->getShippingState(),
                'ship_postal_code'       => $shippingAddress->getZipcode(),
                'ship_country'           => $this->getCountryField($shippingAddress),
                'ship_phone'             => $this->getProfile()->getBillingAddress()->getPhone(),
            ];
        }

        $i = 0;

        foreach ($this->getOrder()->getItems() as $item) {
            $product = $item->getProduct();

            $i++;

            $fields['id' . $i]          = $product->getProductId();
            $fields['description' . $i] = strip_tags(substr($item->getName(), 0, 254));
            $fields['quantity' . $i]    = $item->getAmount();
            $fields['price' . $i]       = $this->getFormattedPrice($item->getNetPrice());
            $fields['subtotal' . $i]    = number_format($item->getSubtotal(), 2, '.', '');
        }

        $this->getLogger('XC-ESelectHPP')->debug('', [
            'fields' => $fields
        ]);

        return $fields;
    }
}
