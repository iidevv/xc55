<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Payment\Processor;

use XLite\InjectLoggerTrait;
use XLite\Model\Payment\BackendTransaction;
use XC\MultiVendor\Model\ProfileTransaction;

/**
 * Paypal IPN processor (helper class)
 */
class PaypalIPN extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * IPN statuses
     */
    public const IPN_VERIFIED      = 'verify';
    public const IPN_DECLINED      = 'decline';
    public const IPN_REQUEST_ERROR = 'request_error';

    /**
     * Return true if received callback request is Paypal IPN
     *
     * @return boolean
     */
    public function isCallbackIPN()
    {
        return !empty(\XLite\Core\Request::getInstance()->payment_status);
    }

    public function isCallbackAdaptiveIPN()
    {
        $isPayCallback = !empty(\XLite\Core\Request::getInstance()->transaction_type)
            && strtolower(urldecode(\XLite\Core\Request::getInstance()->transaction_type)) === 'adaptive payment pay';

        $isRefundCallback = !empty(\XLite\Core\Request::getInstance()->transaction_type)
            && strtolower(urldecode(\XLite\Core\Request::getInstance()->transaction_type)) === 'adjustment'
            && strtolower(urldecode(\XLite\Core\Request::getInstance()->reason_code)) === 'refund';

        return $isPayCallback || $isRefundCallback;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction    $transaction Callback-owner transaction
     * @param \XLite\Model\Payment\Base\Processor $processor   Payment processor object
     *
     * @return bool
     * @throws \XLite\Core\Exception\PaymentProcessing\ACallbackException
     */
    public function tryProcessCallbackIPN($transaction, $processor)
    {
        $result = false;
        // Hack to defer IPN processing on after payment return or ttl expire.
        // Because we can't reliably process IPN right now.
        if ($this->canProcessIPN($transaction)) {
            // If callback is IPN request from Paypal
            $result = $this->processCallbackIPN($transaction, $processor);
        }

        if (!$result) {
            throw new \XLite\Core\Exception\PaymentProcessing\CallbackNotReady();
        }

        return $result;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     * @param callable                         $callable
     *
     * @return bool
     * @throws \XLite\Core\Exception\PaymentProcessing\ACallbackException
     */
    public function tryProcessWithLock($transaction, $callable)
    {
        $result = false;
        // Hack to defer IPN processing on after payment return or ttl expire.
        // Because we can't reliably process IPN right now.
        if ($this->canProcessIPN($transaction)) {
            // If callback is IPN request from Paypal
            $result = $callable();
        }

        if (!$result) {
            throw new \XLite\Core\Exception\PaymentProcessing\CallbackNotReady();
        }

        return $result;
    }

    /**
     * Check if we can process IPN right now or should receive it later
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return boolean
     */
    protected function canProcessIPN(\XLite\Model\Payment\Transaction $transaction)
    {
        // Don't inline the $lock variable https://github.com/symfony/symfony/issues/32062#issuecomment-502681215
        // the code like `$transaction->createEntityAutoLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)->acquire();` won't work
        if (!$this->isOrderProcessed($transaction)) {
            // lock other concurrent IPNs.  use-case1 BUG-4606
            $lock = $transaction->createEntityAutoLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
            $lock->acquire();
            $result = false;
        } elseif (
            ($lock = $transaction->createEntityAutoLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN))
            && $lock->acquire()
        ) {
            // use-case2-3 XCN-8202(XCN-8118) -> BUG-5778
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Checks if the order of transaction is already processed and is available for IPN receiving
     *
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return bool
     */
    protected function isOrderProcessed(\XLite\Model\Payment\Transaction $transaction)
    {
        return !$transaction->isOpen()
            && !$transaction->isInProgress()
            && $transaction->getOrder()->getOrderNumber();
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction    $transaction Callback-owner transaction
     * @param \XLite\Model\Payment\Base\Processor $processor   Payment processor object
     *
     * @return boolean
     */
    public function processCallbackIPN($transaction, $processor)
    {
        $request = \XLite\Core\Request::getInstance();

        $this->getLogger('CDev-Paypal')->debug('processCallbackIPN()', $request->getPostDataWithArrayValues());

        $status                      = $transaction::STATUS_FAILED;
        $registerOriginalTransaction = true;
        $registerBackendTransaction  = false;
        $isRefundCode                = strtolower($request->reason_code) === 'refund';

        switch ($this->getIPNVerification()) {
            case self::IPN_DECLINED:
                $status = $transaction::STATUS_FAILED;
                $processor->markCallbackRequestAsInvalid(static::t('IPN verification failed'));

                break;

            case self::IPN_REQUEST_ERROR:
                $status = $transaction::STATUS_PENDING;
                $processor->markCallbackRequestAsInvalid(static::t('IPN HTTP error'));

                break;

            case self::IPN_VERIFIED:
                $backendTransaction = null;

                // Try to get related backend transaction by PPREF
                $ppref = \XLite\Core\Database::getRepo('XLite\Model\Payment\BackendTransactionData')
                    ->findOneBy(
                        [
                            'name'  => 'PPREF',
                            'value' => $request->txn_id,
                        ]
                    );

                if (!$ppref) {
                    // Try to get related backend transaction by PAYMENTINFO_0_TRANSACTIONID
                    $ppref = \XLite\Core\Database::getRepo('XLite\Model\Payment\BackendTransactionData')
                        ->findOneBy(
                            [
                                'name'  => 'PAYMENTINFO_0_TRANSACTIONID',
                                'value' => $request->txn_id,
                            ]
                        );
                }

                if ($ppref) {
                    $backendTransaction = $ppref->getTransaction();
                }

                $paymentStatus = $this->isCallbackAdaptiveIPN()
                    ? $request->status
                    : $request->payment_status;

                switch (strtolower($paymentStatus)) {
                    case 'completed':
                    case 'canceled_reversal':
                    case 'processed':
                        if ($isRefundCode && $this->isCallbackAdaptiveIPN()) {
                            $status = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;

                            $backendTransactions = $this->proceedAdaptiveRefund(
                                $request,
                                $transaction
                            );

                            $processor->updateInitialBackendTransaction($transaction, $status);

                            foreach ($backendTransactions as $bt) {
                                $bt->registerTransactionInOrderHistory('callback, IPN');
                            }

                            $registerOriginalTransaction = false;
                        } else {
                            $status = $transaction::STATUS_SUCCESS;

                            $transTypes = [
                                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH,
                                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE,
                            ];

                            if (in_array($transaction->getType(), $transTypes)) {
                                if (
                                    $transaction->getType() == \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH
                                    && !isset($backendTransaction)
                                ) {
                                    $backendTransaction = $this->registerBackendTransaction(
                                        $transaction,
                                        \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE
                                    );
                                }

                                $backendTransactionStatus = $transaction::STATUS_SUCCESS;
                            }
                        }

                        break;

                    case 'pending':
                        if (
                            $request->pending_reason === 'authorization'
                            && $transaction->getType() == BackendTransaction::TRAN_TYPE_AUTH
                        ) {
                            $status = $transaction::STATUS_SUCCESS;

                            if (isset($backendTransaction)) {
                                $backendTransactionStatus = $transaction::STATUS_SUCCESS;
                            }
                        } elseif ($isRefundCode) {
                            $registerOriginalTransaction = false;
                            $registerBackendTransaction  = true;
                            $backendTransactionStatus    = BackendTransaction::STATUS_PENDING;
                            $status                      = $transaction::STATUS_SUCCESS;

                            if (!isset($backendTransaction)) {
                                $refundAmount = -1 * (float) $request->mc_gross;
                                $fullAmount = $transaction->getValue();

                                $backendTransaction = $this->registerRefundBackendTransaction($refundAmount, $fullAmount, $transaction);
                            }
                        } else {
                            $status = $transaction::STATUS_PENDING;
                        }

                        break;

                    case 'expired':
                        if ($transaction->getType() == \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH) {
                            $status = $transaction::STATUS_FAILED;

                            if (isset($backendTransaction)) {
                                $backendTransactionStatus = $transaction::STATUS_FAILED;
                            }
                        }

                        break;

                    case 'voided':
                        if ($transaction->getType() == \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH) {
                            $status = $transaction::STATUS_VOID;
                        }

                        if (!isset($backendTransaction)) {
                            $backendTransaction = $this->registerBackendTransaction(
                                $transaction,
                                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID
                            );
                        }

                        $backendTransactionStatus = $transaction::STATUS_SUCCESS;

                        break;

                    case 'denied':
                    case 'reversed':
                        $status = $transaction::STATUS_FAILED;

                        break;

                    case 'failed':
                    case 'cancelled_reversal':
                        if (isset($backendTransaction)) {
                            $backendTransactionStatus = $transaction::STATUS_FAILED;
                        } elseif ($isRefundCode) {
                            $backendTransaction = $this->getPendingRefundTransaction($transaction);
                            $backendTransactionStatus = BackendTransaction::STATUS_FAILED;
                            $status = $transaction::STATUS_SUCCESS;
                        }

                        break;

                    case 'refunded':
                        $registerOriginalTransaction = false;
                        $registerBackendTransaction  = true;
                        $status = $transaction::STATUS_SUCCESS;

                        $backendTransaction = $this->getPendingRefundTransaction($transaction);
                        $backendTransactionStatus = BackendTransaction::STATUS_SUCCESS;
                        if (!isset($backendTransaction)) {
                            $refundAmount = -1 * (float) $request->mc_gross;
                            $fullAmount = $transaction->getValue();

                            $backendTransaction = $this->registerRefundBackendTransaction($refundAmount, $fullAmount, $transaction);
                        }

                        break;

                    default:
                        // No default actions
                }
        }

        if ($transaction->getStatus() != $status) {
            $transaction->setStatus($status);
        }

        if ($registerOriginalTransaction) {
            $transaction->registerTransactionInOrderHistory('callback, IPN');
        }

        if (isset($backendTransactionStatus) && $backendTransaction) {
            if ($backendTransaction->getStatus() != $backendTransactionStatus) {
                $backendTransaction->setStatus($backendTransactionStatus);
            }

            $processor->updateInitialBackendTransaction($transaction, $status);

            if ($registerBackendTransaction) {
                $backendTransaction->registerTransactionInOrderHistory('callback, IPN');
            }
        } elseif (!empty($request->parent_txn_id)) {
            \XLite\Core\OrderHistory::getInstance()->registerTransaction(
                $transaction->getOrder()->getOrderId(),
                sprintf(
                    'IPN received [method: %s, amount: %s, payment status: %s]',
                    $transaction->getPaymentMethod()->getName(),
                    $request->transaction_entity,
                    $request->mc_gross,
                    $request->payment_status
                ),
                $this->getRequestData(),
                'Note: received IPN does not relate to any backend transaction registered with the order. It is possible if you update payment directly on PayPal site or if your customer or PayPal updated the payment.'
            );
        }

        return true;
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return BackendTransaction|null
     */
    protected function getPendingRefundTransaction(\XLite\Model\Payment\Transaction $transaction)
    {
        /** @var BackendTransaction $bt */
        foreach ($transaction->getBackendTransactions() as $bt) {
            if ($bt->isRefund() && $bt->getStatus() === BackendTransaction::STATUS_PENDING) {
                return $bt;
            }
        }

        return null;
    }

    /**
     * @param \XLite\Core\Request              $request
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return \XLite\Model\Payment\BackendTransaction[]
     */
    protected function proceedAdaptiveRefund(\XLite\Core\Request $request, \XLite\Model\Payment\Transaction $transaction)
    {
        $adaptiveData = $request->getPostDataWithArrayValues();

        $transactions = $adaptiveData['transaction'];

        $backendTransactions = [];

        if (is_array($transactions)) {
            foreach ($transactions as $PPAtransaction) {
                $bt = $this->getPPARefundTransaction($PPAtransaction, $transaction);

                if ($bt) {
                    $bt->setStatus(BackendTransaction::STATUS_SUCCESS);
                    $backendTransactions[] = $bt;
                }
            }
        }

        return $backendTransactions;
    }

    /**
     * @param array                            $PPAtransaction
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return null|\XLite\Model\Payment\BackendTransaction
     */
    protected function getPPARefundTransaction($PPAtransaction, \XLite\Model\Payment\Transaction $transaction)
    {
        $refundStatuses = ['refunded', 'partially_refunded'];

        $paypalLogin = $PPAtransaction['receiver'];

        $vendor = !$this->isAdminPaypalLogin($paypalLogin)
            ? $this->getVendorByPaypalLogin($paypalLogin)
            : null;

        $status   = $PPAtransaction['status'];
        $refundId = $PPAtransaction['refund_id'] ?? null;

        $refundAmount = $refundId && isset($PPAtransaction['refund_amount'])
            ? $this->convertAdaptiveAmount($PPAtransaction['refund_amount'])
            : null;

        /** @var \XC\MultiVendor\Model\Order $order */
        $order = $transaction->getOrder();

        $fullAmount = $order && $order->getChildByVendor($vendor)
            ? $order->getChildByVendor($vendor)->getTotal()
            : $transaction->getValue();

        $foundTransaction = $vendor
            ? $this->getInProgressRefundTransaction($vendor->getProfileId(), $transaction)
            : $this->getInProgressRefundTransaction(null, $transaction);

        $backendTransaction = null;
        if (
            $refundAmount
            && in_array(strtolower($status), $refundStatuses, true)
            && !$this->isPPARefundProcessed($refundId, $transaction)
        ) {
            $backendTransaction = $foundTransaction;

            if (!$backendTransaction) {
                $backendTransaction = $this->registerRefundBackendTransaction($refundAmount, $fullAmount, $transaction);

                if ($vendor) {
                    $backendTransaction->setDataCell('vendor_id', $vendor->getProfileId());
                    $backendTransaction->setDataCell('Vendor', $vendor->getLogin());
                }
            }

            $backendTransaction->setDataCell('refund_id', $refundId);

            if ($vendor && $order && !$this->isAdminPaypalLogin($paypalLogin)) {
                $order->createProfileTransaction(
                    -1 * $refundAmount,
                    'PayPal Adaptive: Commission refunded',
                    ProfileTransaction::PROVIDER_PAYPAL,
                    $order->getChildByVendor($vendor)
                );
            }
        }

        return $backendTransaction;
    }

    /**
     * @param string $refundId
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function getInProgressRefundTransaction($vendorId, \XLite\Model\Payment\Transaction $transaction)
    {
        $found = null;

        $backendTransactions = $transaction->getBackendTransactions();
        foreach ($backendTransactions as $backendTransaction) {
            /** @var \XLite\Model\Payment\BackendTransaction $backendTransaction */
            if (!$backendTransaction->isRefund()) {
                continue;
            }

            $isAdminRefundTransaction = $vendorId === null
                && !$backendTransaction->getDataCell('vendor_id');

            $isVendorRefundTransaction = $backendTransaction->getDataCell('vendor_id')
                && (int) ($backendTransaction->getDataCell('vendor_id')->getValue()) === $vendorId;

            if (
                ($isAdminRefundTransaction || $isVendorRefundTransaction)
                && !$backendTransaction->isCompleted()
                && !$backendTransaction->isFailed()
            ) {
                $found = $backendTransaction;
                break;
            }
        }

        return $found;
    }

    /**
     * @param string $refundId
     *
     * @return boolean
     */
    protected function isPPARefundProcessed($refundId, \XLite\Model\Payment\Transaction $transaction)
    {
        $found = false;

        $backendTransactions = $transaction->getBackendTransactions();
        foreach ($backendTransactions as $backendTransaction) {
            /** @var \XLite\Model\Payment\BackendTransaction $backendTransaction */
            if (
                $backendTransaction->getDataCell('refund_id')
                && $backendTransaction->getDataCell('refund_id')->getValue() === $refundId
                && $backendTransaction->isCompleted()
            ) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * @param string $paypalLogin
     *
     * @return \XLite\Model\Profile|null
     */
    protected function getVendorByPaypalLogin($paypalLogin)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->findOneByPaypalLogin($paypalLogin);
    }

    /**
     * @param string $paypalLogin
     *
     * @return boolean
     */
    protected function isAdminPaypalLogin($paypalLogin)
    {
        $method = \CDev\Paypal\Main::getPaymentMethod(
            \CDev\Paypal\Main::PP_METHOD_PAD
        );

        return $method->getSetting('paypal_login') === $paypalLogin;
    }

    /**
     * @param $transactions
     *
     * @return mixed|null
     */
    protected function detectPrimaryTransaction($transactions)
    {
        $primary = null;

        if (is_array($transactions)) {
            if (count($transactions) === 1) {
                $primary = reset($transactions);
            } else {
                foreach ($transactions as $t) {
                    if ($t['is_primary_receiver']) {
                        $primary = $t;
                        break;
                    }
                }
            }
        }

        return $primary;
    }

    /**
     * @param string $adaptiveAmount
     *
     * @return float|null
     */
    protected function convertAdaptiveAmount($adaptiveAmount)
    {
        $explodedAmount = explode(' ', $adaptiveAmount);

        $result = null;

        if (
            $explodedAmount
            && is_array($explodedAmount)
            && count($explodedAmount) === 2
        ) {
            [$currencyCode, $amountInCurrency] = $explodedAmount;

            /** @var \XLite\Model\Currency $currency */
            $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->findOneByCode($currencyCode);

            $result = $currency
                ? (float) $currency->roundValue((float) $amountInCurrency)
                : (float) $amountInCurrency;
        }

        return $result;
    }

    /**
     * getRequestData
     *
     * @return array
     */
    protected function getRequestData()
    {
        $result = [];

        foreach ($this->defineSavedData() as $key => $name) {
            if (isset(\XLite\Core\Request::getInstance()->$key)) {
                $result[] = [
                    'name'  => $key,
                    'value' => \XLite\Core\Request::getInstance()->$key,
                    'label' => $name,
                ];
            }
        }

        return $result;
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        return [
            'secureid'       => 'Transaction id',
            'mc_gross'       => 'Payment amount',
            'payment_type'   => 'Payment type',
            'payment_status' => 'Payment status',
            'pending_reason' => 'Pending reason',
            'reason_code'    => 'Reason code',
            'mc_currency'    => 'Payment currency',
            'auth_id'        => 'Authorization ID',
            'auth_status'    => 'Status of authorization',
            'auth_exp'       => 'Authorization expiration date and time',
            'auth_amount'    => 'Authorization amount',
            'payer_id'       => 'Unique customer ID',
            'payer_email'    => 'Customer\'s primary email address',
            'txn_id'         => 'Original transaction ID',
            'parent_txn_id'  => 'Parent transaction ID',
            'status'         => 'Status',                                       // Adaptive payments
            'fees_payer'     => 'Fees payer',                                   // Adaptive payments
            'sender_email'   => 'Customer\'s primary email address',            // Adaptive payments
            'txnId'          => 'Original transaction identification number',   // Adaptive payments
            'trackingId'     => 'Tracking ID',                                  // Adaptive payments
        ];
    }

    /**
     * Return URL for IPN verification transaction
     *
     * TODO: Check for necessity and remove if not needed
     * @return string
     */
    protected function getIPNURL()
    {
        return $this->getFormURL() . '?cmd=_notify-validate';
    }

    /**
     * Read POST data
     * reading posted data directly from $_POST causes serialization
     * issues with array data in POST. Reading raw POST data from input stream instead.
     * https://github.com/paypal/ipn-code-samples/blob/master/paypal_ipn.php
     *
     * @return string
     */
    protected function getVerificationRequestBody()
    {
        $req = 'cmd=_notify-validate';
        foreach (\XLite\Core\Request::getInstance()->getRawPostData() as $key => $value) {
            $req .= sprintf('&%s=%s', $key, urlencode($value));
        }

        return $req;
    }

    /**
     * Get IPN verification status
     *
     * @return boolean TRUE if verification status is received
     */
    protected function getIPNVerification()
    {
        if ($this->isTestModeEnabled() && \XLite\Core\Request::getInstance()->ignore_verification) {
            return self::IPN_VERIFIED;
        }

        $ipnRequest       = new \XLite\Core\HTTP\Request($this->getFormURL());
        $ipnRequest->verb = 'POST';

        if (function_exists('curl_version')) {
            $ipnRequest->setAdditionalOption(\CURLOPT_SSLVERSION, 1);
            $curlVersion = curl_version();

            if (
                $curlVersion
                && $curlVersion['ssl_version']
                && strpos($curlVersion['ssl_version'], 'NSS') !== 0
            ) {
                $ipnRequest->setAdditionalOption(\CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            }
        }

        $ipnRequest->body = $this->getVerificationRequestBody();

        $ipnRequest->setHeader('User-Agent', 'XCart5');

        $ipnResult = $ipnRequest->sendRequest();

        if ($ipnResult) {
            $this->getLogger('CDev-Paypal')->debug('getIPNVerification()', [
                'data' => $ipnResult->body
            ]);

            $result = (0 < preg_match('/VERIFIED/i', $ipnResult->body))
                ? self::IPN_VERIFIED
                : self::IPN_DECLINED;
        } else {
            $result = self::IPN_REQUEST_ERROR;
        }

        return $result;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->isTestModeEnabled()
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Return TRUE if the test mode is ON
     *
     * @return boolean
     */
    protected function isTestModeEnabled()
    {
        return !empty(\XLite\Core\Request::getInstance()->test_ipn);
    }

    /**
     * Register backend transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction object
     * @param string                           $transactionType Type of backend transaction
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function registerBackendTransaction(\XLite\Model\Payment\Transaction $transaction, $transactionType)
    {
        $backendTransaction = $transaction->createBackendTransaction($transactionType);

        $transactionData   = $this->getRequestData();
        $transactionData[] = [
            'name'  => 'PPREF',
            'value' => \XLite\Core\Request::getInstance()->txn_id,
            'label' => 'Unique PayPal transaction ID (PPREF)',
        ];

        foreach ($transactionData as $data) {
            $backendTransaction->setDataCell(
                $data['name'],
                $data['value'],
                $data['label']
            );
        }

        return $backendTransaction;
    }

    /**
     * Register refund backend transaction
     *
     * @param float                            $refundAmount    refund amount value
     * @param float                            $fullAmount      full amount value
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction object
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function registerRefundBackendTransaction($refundAmount, $fullAmount, \XLite\Model\Payment\Transaction $transaction)
    {
        $backendTransaction = null;

        if ($refundAmount != $fullAmount) {
            $backendTransaction = $this->registerBackendTransaction(
                $transaction,
                BackendTransaction::TRAN_TYPE_REFUND_PART
            );
            $backendTransaction->setValue($refundAmount);
        } else {
            $backendTransaction = $this->registerBackendTransaction(
                $transaction,
                BackendTransaction::TRAN_TYPE_REFUND
            );
        }

        return $backendTransaction;
    }
}
