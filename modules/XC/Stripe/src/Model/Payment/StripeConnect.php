<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Payment;

use Stripe\StripeObject;
use XLite\Core\Config;
use XLite\InjectLoggerTrait;
use XLite\Model\Payment\Transaction;
use XC\Stripe\Main;
use XLite\Model\Payment\BackendTransaction;

/**
 * Stripe payment processor
 */
class StripeConnect extends \XC\Stripe\Model\Payment\AStripe
{
    use InjectLoggerTrait;

    public const API_VERSION = '2020-08-27';

    /**
     * Check - payment method connected to Stripe or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isSettingsConfigured(\XLite\Model\Payment\Method $method)
    {
        $accessToken = $this->isTestMode($method)
            ? $method->getSetting('accessTokenTest')
            : $method->getSetting('accessToken');
        $publishKey  = $this->isTestMode($method)
            ? $method->getSetting('publishKeyTest')
            : $method->getSetting('publishKey');

        return $accessToken && $publishKey;
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
        return $this->isSettingsConfigured($method)
            && \XLite\Core\Config::getInstance()->Security->customer_security
            && \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
            && !Main::getStripeMethod()->getEnabled();
    }

    /**
     * @return string
     */
    public function getActualClientSecret(\XLite\Model\Payment\Method $method)
    {
        $suffix = $this->isTestMode($method) ? 'Test' : '';

        return $method->getSetting('accessToken' . $suffix);
    }

    /**
     * @return string
     */
    public function getActualClientId(\XLite\Model\Payment\Method $method)
    {
        $suffix = $this->isTestMode($method) ? 'Test' : '';

        return $method->getSetting('clientId' . $suffix);
    }

    /**
     * Get allowed backend transactions
     *
     * @return array Status codes
     */
    public function getAllowedTransactions()
    {
        return [
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_REFUND_PART,
            BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XC\Stripe\View\StripeConnectConfig';
    }

    /**
     * Return true if payment method settings form should use default submit button.
     * Otherwise, settings widget must define its own button
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return true;
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
     * Get initial transaction type (used when customer places order)
     *
     * @param \XLite\Model\Payment\Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        return BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * @param $params
     *
     * @return string|null
     */
    public function getVendorOauthLink($params)
    {
        $this->includeStripeLibrary();
        $oauthUrl = null;

        try {
            $oauthUrl = \Stripe\OAuth::authorizeUrl($params);
        } catch (\Exception $e) {
            $this->getLogger('XC-Stripe')->debug('getVendorOauthLink error: ' . $e->getMessage());
        }

        return $oauthUrl;
    }

    /**
     * @param array $params
     *
     * @return StripeObject|null
     */
    public function getConnectedAccountByCode($params)
    {
        $this->includeStripeLibrary();
        $response = null;

        try {
            $response = \Stripe\OAuth::token($params);
        } catch (\Exception $e) {
            $this->getLogger('XC-Stripe')->debug('getConnectedAccountByCode error:' . $e->getMessage());
        }

        return $response;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function deauthorizeVendorAccount($stripeUserId)
    {
        $this->includeStripeLibrary();
        $response = false;

        try {
            $response = \Stripe\OAuth::deauthorize(
                [
                    'stripe_user_id' => $stripeUserId,
                ]
            );
        } catch (\Exception $e) {
            $this->getLogger('XC-Stripe')->debug('deauthorizeVendorAccount error:' . $e->getMessage());
        }

        return !$response->error;
    }

    /**
     * @return array
     */
    protected function getCheckoutSessionParams()
    {
        $currency  = $this->transaction->getCurrency();
        $lineItems = [
            [
                'price_data' => [
                    'currency'     => strtolower($currency->getCode()),
                    'product_data' => [
                        'name' => Config::getInstance()->Company->company_name,
                    ],
                    'unit_amount'  => $this->formatCurrency($this->getOrder()->getTotal()),
                ],
                'quantity'   => 1,
            ],
        ];

        $params = [
            'success_url'          => $this->getReturnURL(null, true),
            'cancel_url'           => $this->getReturnURL(null, true, true),
            'mode'                 => 'payment',
            'payment_method_types' => ['card'],
            'client_reference_id'  => $this->getOrder()->getOrderId(),
            'customer_email'       => $this->getProfile()->getLogin(),
            'line_items'           => $lineItems,
            'payment_intent_data'  => [
                'capture_method' => $this->isCapture() ? 'automatic' : 'manual',
                'description'    => static::t('Payment transaction ID') . ': ' . $this->transaction->getPublicId(),
                'metadata'       => [
                    'txnId' => $this->transaction->getPublicTxnId(),
                ],
            ],
        ];

        $origProfile = $this->getOrder()->getOrigProfile();
        $stripeCustomerId = null;
        if ($origProfile && !$origProfile->getAnonymous()) {
            $stripeCustomerId = $origProfile->getStripeCustomerId();
        }

        $stripeCustomer = $this->updateStripeCustomer($this->getProfile(), $stripeCustomerId);
        if ($stripeCustomer && $stripeCustomer->id) {
            $params['customer'] = $stripeCustomer->id;
            unset($params['customer_email']);
        }

        return $params;
    }

    /**
     * Process return
     *
     * @param Transaction $transaction Return-owner transaction
     */
    public function processReturn(Transaction $transaction)
    {
        parent::processReturn($transaction);

        $transaction->setEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);

        $this->processCompleteCheckout($transaction);
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     */
    public function processCompleteCheckout(\XLite\Model\Payment\Transaction $transaction)
    {
        $this->includeStripeLibrary();

        try {
            $intentId = $transaction->getDetail('stripe_id');
            $intent   = \Stripe\PaymentIntent::retrieve($intentId);

            /** @var \Stripe\Charge $charge */
            $charge = $intent->charges instanceof \Stripe\Collection
                ? $intent->charges->first()
                : null;

            $status   = Transaction::STATUS_FAILED;
            $btStatus = BackendTransaction::STATUS_FAILED;
            $error    = '';
            if (
                in_array($intent->status, ['succeeded', 'requires_capture'])
                && $charge
            ) {
                $status   = Transaction::STATUS_SUCCESS;
                $btStatus = BackendTransaction::STATUS_SUCCESS;
                $transaction->setNote('');

                $transaction->setDataCell('sc_charge_id', $charge->id);
                $this->createChildOrderTransactions($transaction);

                $origProfile = $this->getOrder()->getOrigProfile();
                if (
                    $origProfile
                    && !$origProfile->getAnonymous()
                    && !$origProfile->getStripeCustomerId()
                    && $intent->customer
                ) {
                    $origProfile->setStripeCustomerId($intent->customer);
                }

                if (!$this->checkTotal($transaction->getCurrency()->convertIntegerToFloat($intent->amount))) {
                    $error = "Total amount doesn't match.";
                } elseif (!$this->checkCurrency(strtoupper($intent->currency))) {
                    $error = "Currency code doesn't match.";
                }
            } else {
                $error = 'Invalid PaymentIntent status';

                if ($charge && $charge->failure_message) {
                    $error = $charge->failure_message;
                    $transaction->setNote($error);
                }
            }

            if ($error) {
                $status   = Transaction::STATUS_FAILED;
                $btStatus = BackendTransaction::STATUS_FAILED;
                $transaction->setNote($error);
                $transaction->setDataCell('Error', $error);
            }

            $transaction->setStatus($status);
            $bt = $transaction->getInitialBackendTransaction();
            if (!$bt) {
                $bt = $this->registerBackendTransaction($this->getInitialTransactionType(), $transaction);
            }

            $bt->setStatus($btStatus);
        } catch (\Exception $e) {
            $this->getLogger('XC-Stripe')->debug('Error: ' . __FUNCTION__, [
                'request'          => $this->request->getPostDataWithArrayValues(),
                'exceptionMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Transaction $transaction
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createChildOrderTransactions(Transaction $transaction)
    {
    }

    /**
     * @param BackendTransaction $backendTransaction
     * @param                    $refundAmount
     */
    protected function registerVendorRefund(BackendTransaction $backendTransaction, $refundAmount)
    {
    }

    /**
     * Include Stripe library
     *
     * @return void
     */
    protected function includeStripeLibrary()
    {
        if (!$this->stripeLibIncluded) {
            if ($this->transaction) {
                $method   = $this->transaction->getPaymentMethod();
                $key      = $this->getActualClientSecret($method);
                $clientId = $this->getActualClientId($method);
            } else {
                $method   = Main::getStripeConnectMethod();
                $key      = $this->getActualClientSecret($method);
                $clientId = $this->getActualClientId($method);
            }

            \Stripe\Stripe::setApiKey($key);
            \Stripe\Stripe::setApiVersion(static::API_VERSION);
            \Stripe\Stripe::setClientId($clientId);

            \Stripe\Stripe::setAppInfo(
                static::APP_NAME,
                Main::getVersion(),
                'https://market.x-cart.com/addons/stripe-payment-module.html',
                static::APP_PARTNER_ID
            );

            $this->stripeLibIncluded = true;
        }
    }

    // {{{ Secondary transactions

    /**
     * Refund
     *
     * @param BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefundMulti(BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Refund
     *
     * @param BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefund(BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDetail('stripe_id')
            );

            /** @var \Stripe\Charge $payment */
            $payment = $paymentIntent->charges instanceof \Stripe\Collection
                ? $paymentIntent->charges->first()
                : null;

            if (!$payment) {
                throw new \Exception('No charges found for payment intent ' . $paymentIntent->id);
            }

            $refundAmount = $this->formatCurrency($transaction->getValue());
            if ($transaction->getType() === BackendTransaction::TRAN_TYPE_REFUND) {
                $chargeAvailableAmount      = $payment->amount - $payment->amount_refunded;
                $refundAmount               = $refundAmount > $chargeAvailableAmount
                    ? $chargeAvailableAmount
                    : $refundAmount;
            }

            /** @var \Stripe\Refund $refundObject */
            $refundObject = $payment->refunds->create([
                'amount' => $refundAmount,
            ]);

            $backendTransactionStatus = BackendTransaction::STATUS_SUCCESS;

            $transaction->setDataCell('stripe_date', $refundObject->created);
            $transaction->setDataCell('sc_refund_id', $refundObject->id);
            if ($refundObject->balance_transaction) {
                $transaction->setDataCell('stripe_b_txntid', $refundObject->balance_transaction);
            }

            $refundAmount = $transaction->getPaymentTransaction()->getCurrency()->convertIntegerToFloat($refundObject->amount);
            $this->registerVendorRefund($transaction, $refundAmount);

            $this->getLogger('XC-Stripe')->debug('Success: ' . __FUNCTION__, [
                'id'                  => $refundObject->id,
                'amount'              => $refundObject->amount,
                'balance_transaction' => $refundObject->balance_transaction,
            ]);
        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            $this->getLogger('XC-Stripe')->debug(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);

        return $backendTransactionStatus == BackendTransaction::STATUS_SUCCESS;
    }

    /**
     * Check - specified rfund transaction is registered or not
     *
     * @param object $refund Refund transaction
     *
     * @return boolean
     */
    protected function isRefundTransactionRegistered($refund)
    {
        $result = null;
        $types  = [
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_REFUND_PART,
            BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];

        foreach ($this->transaction->getBackendTransactions() as $bt) {
            $txnid = $bt->getDataCell('stripe_b_txntid');
            if (
                in_array($bt->getType(), $types)
                && (!$txnid || $txnid->getValue() == $refund->balance_transaction)
                && ($bt->getDataCell('stripe_date') && $bt->getDataCell('stripe_date')->getValue() == $refund->created)
            ) {
                $result = $bt;
                break;
            }
        }

        return $result;
    }

    /**
     * @param \Stripe\Event $event
     *
     * @return \Stripe\Refund|null
     */
    protected function getRefundObject($event)
    {
        $refunds = $event->data->object->refunds instanceof \Stripe\Collection
            ? $event->data->object->refunds->data
            : $event->data->object->refunds;

        foreach ($refunds as $r) {
            if (!$this->isRefundTransactionRegistered($r)) {
                return $r;
            }
        }

        return null;
    }

    // }}}

    // {{{ Callback

    /**
     * @param $name
     * @param $value
     *
     * @return BackendTransaction
     */
    protected function getSCBackendTransaction($name, $value)
    {
        $repo   = \XLite\Core\Database::getRepo('\XLite\Model\Payment\BackendTransaction');
        $result = $repo->scFindOneByCell($name, $value);

        return $result;
    }

    // }}}
}
