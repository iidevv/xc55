<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor;

use Qualiteam\SkinActXPaymentsConnector\Core\Iframe;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Model\Payment\Method;
use XLite\Model\Payment\Transaction;

/**
 * XPayments payment processor
 */
class XPayments extends AXPayments
{
    /**
     * Form fields
     *
     * @var array
     */
    protected $formFields;

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/save_card_box.twig';
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return [
            'name',
            'id',
            'sale',
            'auth',
            'capture',
            'capturePart',
            'captureMulti',
            'void',
            'voidPart',
            'voidMulti',
            'refund',
            'refundPart',
            'refundMulti',
            'getInfo',
            'accept',
            'decline',
            'test',
            'authExp',
            'captMinLimit',
            'captMaxLimit',
            'moduleName',
            'settingsHash',
            'saveCards',
            'canSaveCards',
            'currency',
            'isTestMode',
        ];
    }

    /**
     * Process return
     *
     * @param Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $txnId = Request::getInstance()->txnId;

        // Forces xpc_txnid to be actual in case of duplicate transaction issues
        $transaction->setDataCell('xpc_txnid', $txnId, 'X-Payments transaction ID', 'C');
        // Initialize transaction status
        $transaction->setStatus($transaction::STATUS_FAILED);

        $info = $this->client->requestPaymentInfo($txnId);

        $this->client->clearInitDataFromSession();

        if ($info->isSuccess()) {

            $response = $info->getResponse();

            if (version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.9') >= 0) {
                $response = $response['payment'];
            }

            $transaction->setDataCell('xpc_message', $response['message'], 'X-Payments status');

            if ($response['isFraudStatus']) {
                $transaction->setDataCell('xpc_fmf', 'Triggered', 'X-Payments fraud check');
            }

            $errorDescription = false;

            if (abs($response['amount'] - $transaction->getValue()) > 0.01) {

                // Total wrong
                $errorDescription = 'Total amount doesn\'t match. '
                    . 'Transaction total: ' . $transaction->getValue() . ', '
                    . 'X-Payments amount: ' . $response['amount'];
            }

            if (!$transaction->getCurrency()) {

                // Adjust currency if it's not set
                $currency = \XLite::getInstance()->getCurrency();
                $transaction->setCurrency($currency);
            }

            if ($response['currency'] != $transaction->getCurrency()->getCode()) {

                // Currency wrong
                $errorDescription = 'Currency doesn\'t match. '
                    . 'Transaction currency: ' . $transaction->getCurrency()->getCode() . ', '
                    . 'X-Payments currency: ' . $response['currency'];
            }

            if ($errorDescription) {

                // Set error details, status remails Failed
                $transaction->setDataCell('error', 'Hacking attempt!', 'Error');
                $transaction->setDataCell('errorDescription', $errorDescription, 'Hacking attempt details');

            } else {

                // Set the transaction status and type
                $transaction->setStatus($this->getTransactionStatus($response, $transaction));

                $this->setTransactionTypeByStatus($transaction, $response['status']);

            }

            // API 1.5 backwards compatibility masked card data parsing
            // For newer versions it will be saved in processTransactionUpdate()
            if (
                empty($response['maskedCardData'])
                && Request::getInstance()->last_4_cc_num
                && Request::getInstance()->card_type

            ) {
                $transaction->saveCard(
                    '******',
                     Request::getInstance()->last_4_cc_num,
                     Request::getInstance()->card_type
                );

                // Now set current address for saved card
                $profile = $transaction->getOrder()->getOrigProfile();
                if ($profile) {
                    $address = null;
                    if ($profile->getBillingAddress()) {
                        $address = $profile->getBillingAddress();
                    } elseif ($profile->getShippingAddress()) {
                        $address = $profile->getShippingAddress();
                    }
                    if ($address) {
                        $transaction->getXpcData()->setBillingAddress($address);
                    }
                }
            }

            $this->processTransactionUpdate($transaction, $response, $txnId);

            if (
                !empty($response['saveCard'])
                && version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.6') >= 0
                && $transaction->getXpcData()
            ) {
                $transaction->getXpcData()->setUseForRecharges(($response['saveCard'] == 'Y') ? 'Y' : 'N');
            }

        }

        // AntiFraud check block by AVS
        if (
            method_exists($transaction, 'checkAvsDataValid')
            && $transaction->checkAvsDataValid()
        ) {

            $result = $transaction->getAntiFraudResult();

            if ($transaction->checkBlockAvs()) {
                $result->setDataCell('blocked_by_avs', '1');
                $result->setScore($result::MAX_SCORE);
            } else {
                $result->setDataCell('blocked_by_avs', '0');
            }
        }

        $transaction->setXpcDataCell('xpc_deny_callbacks', '0');

        Database::getEM()->flush();
    }

    /**
     * This is not Saved Card payment method
     *
     * @return boolean
     */
    protected function isSavedCardsPaymentMethod()
    {
        return false;
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $status = parent::doInitialPayment();
        if (
            static::PROLONGATION == $status
            && Iframe::getInstance()->useIframe()
        ) {
            exit ();
        }

        return $status;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        $config = Config::getInstance()->Qualiteam->SkinActXPaymentsConnector;

        return preg_replace('/\/+$/Ss', '', $config->xpc_xpayments_url) . '/payment.php';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $this->formFields = $this->client->getFormFields($this->transaction);

        return $this->formFields;
    }

    /**
     * Save some payment settings from payment method to the transaction
     *
     * @param Transaction $transaction Transaction
     *
     * @return string
     */
    public function savePaymentSettingsToTransaction(Transaction $transaction, $parentTransaction = null)
    {
        $firstSetting = reset($this->paymentSettingsToSave);

        if (!$transaction->getXpcDataCell('xpc_can_do_' . $firstSetting)) {
            // Write XPC data only if it is not present yet

            if ($parentTransaction) {
                $paymentMethod = $parentTransaction->getPaymentMethod();
            } else {
                $paymentMethod = $transaction->getPaymentMethod();
            }

            foreach ($this->paymentSettingsToSave as $field) {
                $key = 'xpc_can_do_' . $field;
                if ($paymentMethod->getSetting($field)) {
                    $transaction->setXpcDataCell($key, $paymentMethod->getSetting($field));
                }
            }
        }
    }

    /**
     * Do redirect to X-Payments payment form
     *
     * @param Transaction $transaction Transaction
     * @param array                            $request     Input data request OPTIONAL
     *
     * @return string
     */
    public function pay(Transaction $transaction, array $request = [])
    {
        $this->savePaymentSettingsToTransaction($transaction);

        if (version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.6') < 0) {
            Session::getInstance()->cardSavedAtCheckout = Request::getInstance()->save_card;
        }

        return parent::pay($transaction, $request);
    }

    /**
     * Check - payment method has enabled test mode or not
     *
     * @param Method $method Payment method
     *
     * @return boolean
     */
    public function isTestMode(Method $method)
    {
        return 'Y' == $method->getSetting('isTestMode');
    }
}
