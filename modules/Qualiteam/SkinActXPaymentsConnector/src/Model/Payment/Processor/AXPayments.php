<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor;

use Includes\Utils\FileManager;
use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\FraudCheckData;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use XLite\Core\CommonCell;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Exception\PaymentProcessing\CallbackNotReady;
use XLite\Core\Exception\PaymentProcessing\CallbackRequestError;
use XLite\Core\Layout;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\InjectLoggerTrait;
use XLite\Model\Order;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Base\WebBased;
use XLite\Model\Payment\Method;
use XLite\Model\Payment\Transaction;
use XLite\Model\Payment\TransactionData;

/**
 * XPayments payment processor
 */
abstract class AXPayments extends WebBased
{
    use InjectLoggerTrait;

    /**
     * Payment statuses
     */
    const STATUS_NEW            = 1;
    const STATUS_AUTH           = 2;
    const STATUS_DECLINED       = 3;
    const STATUS_CHARGED        = 4;
    const STATUS_REFUND         = 5;
    const STATUS_REFUND_PART    = 6;

    /**
     * Payment processor classes
     */
    const METHOD_SAVED_CARD = SavedCard::class;
    const METHOD_XPAYMENTS  = XPayments::class;

    /**
     * Log file name for callback requests
     */
    const LOG_FILE_NAME = 'xp-connector-callback';

    /**
     * X-Payments client
     *
     * @var XPaymentsClient
     */
    protected $client;

    /**
     * Detected transaction 
     *
     * @var Transaction
     */
    protected $detectedTransaction = null;

    /**
     * This is not Saved Card payment method
     *
     * @return boolean
     */
    abstract protected function isSavedCardsPaymentMethod();

    /**
     * Payment method has settings into Module settings section
     *
     * @return boolean
     */
    public function hasModuleSettings()
    {
        return true;
    }

    /**
     * Map for order payment actions: X-Payments' client method and order status 
     */
    protected $secondaryActionsMap = array(
        'capture' => array(
            'method' => 'requestPaymentCapture',
            'action' => 'captured',
        ),
        'void' => array(
            'method' => 'requestPaymentVoid',
            'action' => 'voided', 
        ),
        'refund' => array(
            'method' => 'requestPaymentRefund',
            'action' => 'refunded',
        ),
        'accept' => array(
            'method' => 'requestPaymentAccept',
            'action' => 'accepted',
        ),
        'decline' => array(
            'method' => 'requestPaymentDecline',
            'action' => 'declined',
        ),
    );

    /**
     * List of payment method settings which should be saved in transaction
     */
    protected $paymentSettingsToSave = array(
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
    );

    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        $types = array(
            static::OPERATION_SALE,
            static::OPERATION_AUTH,
            static::OPERATION_CAPTURE,
            static::OPERATION_CAPTURE_PART,
            static::OPERATION_CAPTURE_MULTI,
            static::OPERATION_VOID,
            static::OPERATION_VOID_PART,
            static::OPERATION_VOID_MULTI,
            static::OPERATION_REFUND,
            static::OPERATION_REFUND_PART,
            static::OPERATION_REFUND_MULTI,
        );

        foreach ($types as $k => $v) {
            if (!$this->transaction->getPaymentMethod()->getSetting($v)) {
                unset($types[$k]);
            }
        }

        return $types;
    }

    /**
     * Check - payment method is configured or not
     *
     * @param Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(Method $method)
    {
        return parent::isConfigured($method)
            && XPaymentsClient::getInstance()->isModuleConfigured();
    }

    /**
     * Get payment method configuration page URL
     *
     * @param Method $method    Payment method
     * @param boolean                     $justAdded Flag if the method is just added via administration panel. Additional init configuration can be provided
     *
     * @return string
     */
    public function getConfigurationURL(Method $method, $justAdded = false)
    {
        return ($this->getModuleId())
            ? Converter::buildURL(
                'xpc',
                '',
                ['section' => 'payment_methods']
            )
            : parent::getConfigurationURL($method, $justAdded);
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param Method $method Payment method
     *
     * @return string | boolean
     */
    public function getAdminIconURL(Method $method)
    {
        if (false !== strpos($method->getServiceName(), 'XPayments.Allowed')) {
            $name = $method->getServiceName();
        } else {
            $name = str_replace('XPayments', 'XPayments.Allowed', $method->getServiceName());
        }

        $file = 'modules/Qualiteam/SkinActXPaymentsConnector/icons/' . $name . '.png';
        $filePath = LC_DIR_SKINS . 'admin' . LC_DS . str_replace('/', LC_DS, $file);

        return FileManager::isExists($filePath)
            ? Layout::getInstance()->getResourceWebPath($file)
            : true;
    }

    /**
     * Process Kount data from callback
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array $transactionData Transaction data
     *
     * @return void
     */
    protected function processKountData(Transaction $transaction, $transactionData)
    {
        $prefix = '[Kount] ';

        $texts = array(
            'D' => 'High fraud risk detected',
            'A' => 'Antifraud check passed',
            'R' => 'Manual Review required',
        );

        $statuses = array(
            'D' => Order::FRAUD_STATUS_FRAUD,
            'A' => Order::FRAUD_STATUS_CLEAN,
            'R' => Order::FRAUD_STATUS_REVIEW,
        );

        $result = false;

        foreach ($transactionData['fields'] as $field) {

            if (strpos($field['name'], $prefix) === 0) {

                if (!$result) {
                    $result = new \stdClass;
                }

                $key = trim(str_replace($prefix, '', $field['name']));

                if (is_numeric($key)) {

                    if (!$result->rules) {
                        $result->rules = new \stdClass;
                    }

                    $result->rules->$key = $field['value'];

                } elseif ('Auto' == $key) {

                    $result->message = isset($texts[$field['value']]) ? $texts[$field['value']] : '';
                    $transaction->getOrder()->setFraudStatusXpc(
                        isset($statuses[$field['value']]) 
                            ? $statuses[$field['value']] 
                            : ''
                    );

                    $transaction->getOrder()->setFraudTypeXpc(FraudCheckData::CODE_KOUNT);

                    $result->Auto = $field['value'];

                } elseif (
                    strpos($key, 'ERROR') === 0
                    || strpos($key, 'Internal error') === 0
                ) {

                    if (!$result->errors) {
                        $result->errors = new \stdClass;
                    }

                    $result->errors->$key = $field['value'];

                } else {
                    $result->$key = $field['value'];
                }
            }
        }

        if (
            $result
            && $result->errors
            && !$result->Auto
        ) {
            $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_FRAUD);
            $transaction->getOrder()->setFraudTypeXpc(FraudCheckData::CODE_KOUNT);
        }

        return $result;
    }

    /**
     * Process Fraud Check data from callback
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array $data Data
     *
     * @return void
     */
    protected function processFraudCheckData(Transaction $transaction, $data)
    {
        if (
            empty($data['fraudCheckData'])
            || !is_array($data['fraudCheckData'])
        ) {
            return;
        }

        foreach ($transaction->getFraudCheckData() as $fraudCheckData) {
            Database::getEM()->remove($fraudCheckData);
        }

        // Maximum fraud result within several serices (if there are more than one)
        $maxFraudResult = FraudCheckData::RESULT_UNKNOWN;

        // Code of the service which got "most fraud" result
        $maxFraudResultCode = '';

        // Flag to check if any errors which prevented fraud check occurred 
        $errorsFound = false;

        foreach ($data['fraudCheckData'] as $service) {

            // Ignore "noname" services. This must be filled in on the X-Payments side
            if (empty($service['code']) || empty($service['service'])) {
                continue;
            }

            if (empty($maxFraudResultCode)) {
                // Use first the code, so that something is specified
                $maxFraudResultCode = $service['code'];
            }

            $fraudCheckData = new FraudCheckData;
            $fraudCheckData->setTransaction($transaction);

            $transaction->addFraudCheckData($fraudCheckData);

            $fraudCheckData->setCode($service['code']);
            $fraudCheckData->setService($service['service']);

            $module = '';
            switch ($fraudCheckData->getCode()) {
                case FraudCheckData::CODE_ANTIFRAUD:
                    $module = $service['module'];
                    break;
                case FraudCheckData::CODE_KOUNT:
                    $module = 'Kount';
                    break;
                case FraudCheckData::CODE_NOFRAUD:
                    $module = 'NoFraud';
                    break;
            }
            if (
                version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.8') < 0
                && $module
            ) {
                // Convert old api service code to modern
                $fraudCheckData->setCode(FraudCheckData::CODE_ANTIFRAUD);
            }
            $fraudCheckData->setModule($module);

            if (!empty($service['result'])) {
                $fraudCheckData->setResult($service['result']);

                if (intval($service['result']) > $maxFraudResult) {
                    $maxFraudResult = intval($service['result']);
                    $maxFraudResultCode = $service['code'];
                }
            }

            if (!empty($service['status'])) {
                $fraudCheckData->setStatus($service['status']);
            }

            if (!empty($service['score'])) {
                $fraudCheckData->setScore($service['score']);
            }

            if (!empty($service['transactionId'])) {
                $fraudCheckData->setServiceTransactionId($service['transactionId']); 
            }

            if (!empty($service['url'])) {
                $fraudCheckData->setUrl($service['url']);
            }

            if (!empty($service['message'])) {
                $fraudCheckData->setMessage($service['message']);

                if (FraudCheckData::RESULT_UNKNOWN == $service['result']) {
                    // Unknown result with message should be shown as error
                    $errorsFound = true;
                }
            }

            if (!empty($service['errors'])) {
                $errors = implode("\n", $service['errors']);
                $fraudCheckData->setErrors($errors);
                $errorsFound = true;
            }

            if (!empty($service['rules'])) {
                $rules = implode("\n", $service['rules']);
                $fraudCheckData->setRules($rules);
            }

            if (!empty($service['warnings'])) {
                $warnings = implode("\n", $service['warnings']);
                $fraudCheckData->setWarnings($warnings);
            }
        }

        // Convert maximun fraud result to the order's fraud status
        switch ($maxFraudResult) {

            case FraudCheckData::RESULT_UNKNOWN:
                if ($errorsFound) {
                    $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_ERROR);
                } else {
                    $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_UNKNOWN);
                }
                break;

            case FraudCheckData::RESULT_ACCEPTED:
                $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_CLEAN);
                break;

            case FraudCheckData::RESULT_MANUAL:
            case FraudCheckData::RESULT_PENDING:
                $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_REVIEW);
                break;

            case FraudCheckData::RESULT_FAIL:
                $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_FRAUD);
                break;
        }

        // Set type of the fraud system
        $transaction->getOrder()->setFraudTypeXpc($maxFraudResultCode);

        // Set transaction iD for fraud check data to order
        $transaction->getOrder()->setFraudCheckTransactionId($transaction->getTransactionId());
    }

    /**
     * Get text label for card validation data 
     *
     * @param string $status Status code. See XPay_Model_PaymentCardValidationData
     *
     * @return string 
     */
    protected static function getCardValidationText($status)
    {
        static $text = array(
            '0' => 'Not checked',
            '1' => 'Match',
            '2' => 'Mismatch',
        );
        
        return $text[$status];
    }

    /**
     * Process card validation data
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array $data Data
     *
     * @return void
     */
    protected function processCardValidationData(Transaction $transaction, $data)
    {
        $cardValidation = array(
            'avs_z' => 'AVS ZIP/Postal code',
            'avs_a' => 'AVS Address (street)',
            'avs_c' => 'AVS Cardholder name (optional)',
            'cvv' => 'CVV2/CVD/Card secure code'
        );

        if (
            isset($data['cardValidation'])
            && !empty($data['cardValidation'])
            && is_array($data['cardValidation'])
        ) {

            foreach ($cardValidation as $key => $label) {

                if (isset($data['cardValidation'][$key])) {
                    $transaction->setXpcDataCell('xpc_' . $key, $data['cardValidation'][$key]);
                    $transaction->setDataCell('xpc_' . $key . '_text', self::getCardValidationText($data['cardValidation'][$key]), $label, 'C');
                }
            }
        }

    }

    /**
     * Process masked card data
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array $data Data
     *
     * @return void
     */
    protected function processMaskedCardData(Transaction $transaction, $data)
    {
        if (
            isset($data['maskedCardData'])
            && !empty($data['maskedCardData'])
            && is_array($data['maskedCardData'])
        ) {

            $data = $data['maskedCardData'];

            $transaction->saveCard(
                $data['first6'],
                $data['last4'],
                $data['type'],
                $data['expire_month'],
                $data['expire_year']
            );

            Database::getEM()->flush();

            if (!$transaction->getXpcData()->getBillingAddress()) {
                $profile = $transaction->getOrder()->getOrigProfile();
                if ($profile) {
                    if (0 === $profile->getDefaultCardId()) {
                        $card = $transaction->getCard();
                        $profile->setDefaultCardId($card['card_id']);
                    }
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

            $address = $transaction->getXpcData()->getBillingAddress();

            if ($address) {
                if (!empty($data['b_address'])) {
                    $address->setStreet($data['b_address']);
                }

                if (!empty($data['b_city'])) {
                    $address->setCity($data['b_city']);
                }

                if (!empty($data['b_zipcode'])) {
                    $address->setZipcode($data['b_zipcode']);
                }

                if (!empty($data['b_phone'])) {
                    $address->setPhone($data['b_phone']);
                }

                // TODO: match raw b_country and b_state as well
            }

        }
    }

    /**
     * Process 3-D Secure data
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array $data Data
     *
     * @return void
     */
    protected function process3dSecureData(Transaction $transaction, $data)
    {
        if (
            isset($data['3dsecure'])
            && !empty($data['3dsecure'])
            && is_array($data['3dsecure'])
        ) {
            
            $s3dMessage = '';

            foreach ($data['3dsecure'] as $s3dKey => $s3dValue) {
                if (!empty($s3dValue)) {
                    $s3dMessage .= $s3dKey . ': ' . $s3dValue . PHP_EOL;
                    $transaction->setXpcDataCell('xpc_' . $s3dKey, $s3dValue);
                }
            }

            $label = static::t('3-D Secure');

            $transaction->setDataCell('xpc_3dsecure_text', $s3dMessage, $label, 'C');
        }

    }

    /**
     * Process callback/initial data to update transaction details
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array $data API response or parsed payment info data
     * @param string $txnId X-Payments Transaction Id (used only for API < 1.9)
     *
     * @return void
     */
    protected function processTransactionUpdate(Transaction $transaction, $data, $txnId)
    {
        if (
            array_key_exists('payment', $data)
            && version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.9') >= 0
        ) {
            // This is result of API 1.9 request, get transaction info from it directly
            $data = $data['payment'];
        } elseif (
            !array_key_exists('chargedAmount', $data)
            && $txnId
        ) {
            // This is result of older API request, request transaction info from X-Payments
            $info = $this->client->requestPaymentInfo($txnId);
            if ($info->isSuccess()) {
                $data = $info->getResponse();
            }
        }

        if (empty($data) || !array_key_exists('chargedAmount', $data)) {
            $this->getLogger('Qualiteam-SkinActXPaymentsConnector')
                ->error('Process transaction update failed!');
            return;
        }

        $transaction->setDataCell('status', 'Transaction successful', 'X-Payments message', 'C');

        if (isset($data['advinfo']) && is_array($data['advinfo'])) {

            if (!empty($data['advinfo']['Error'])) {
                $transaction->setDataCell('status', $data['advinfo']['Error'], 'X-Payments error', 'C');
                $transaction->setNote($data['advinfo']['Error']);
            }

            if (!empty($data['advinfo']['Message'])) {
                $transaction->setDataCell('status', $data['advinfo']['Message'], 'X-Payments message', 'C');
                $transaction->setNote($data['advinfo']['Message']);
            }
        }

        $transactionData = array(
            'xpc_authorized'        => $data['authorized'],
            'xpc_captured'          => $data['capturedAmount'],
            'xpc_charged'           => $data['chargedAmount'],
            'xpc_voided'            => $data['voidedAmount'],
            'xpc_refunded'          => $data['refundedAmount'],
            'xpc_can_capture'       => $data['capturedAmountAvail'],
            'xpc_can_void'          => $data['voidedAmountAvail'], 
            'xpc_can_refund'        => $data['refundedAmountAvail'],
            'xpc_is_fraud_status'   => $data['isFraudStatus'],
        );   

        if (version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.9') < 0) {
            $transactionData['xpc_charged'] += $data['refundedAmount'];
        }

        if (
            isset($data['isFraudStatus'])
            && '1' == $data['isFraudStatus']
        ) {
            // Set default fraud "review" for the backwards compatibility
            $transaction->getOrder()->setFraudTypeXpc(FraudCheckData::CODE_GATEWAY);
            $transaction->getOrder()->setFraudStatusXpc(Order::FRAUD_STATUS_REVIEW);
        }

        if (
            version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.6') <= 0
            && $transaction->getDataCell('xpc_txnid')
        ) {

            // API 1.6 backwards compatiblity Kount info update

            $transactions = array();
            $kountData = false;

            $info = $this->client->requestPaymentAdditionalInfo($transaction->getDataCell('xpc_txnid')->getValue());    

            if ($info->isSuccess()) {
                $response = $info->getResponse();
                if (
                    !empty($response['transactions'])
                    && is_array($response['transactions'])
                ) {
                    $transactions = $response['transactions'];
                }
            }

            // Search for KOUNT data thru transactions
            foreach ($transactions as $tr) {

                if (
                    isset($tr['fields'])
                    && !empty($tr['fields'])
                    && is_array($tr['fields'])
                ) {
                    $kountData = $this->processKountData($transaction, $tr);

                    if ((bool)$kountData) {
                        $transactionData['xpc_kount'] = serialize($kountData);
                        break;
                    }
                }
            }
        }

        foreach ($transactionData as $key => $value) {
            $transaction->setXpcDataCell($key, $value);
        }

        $this->processCardValidationData($transaction, $data);

        $this->processMaskedCardData($transaction, $data);

        $this->process3dSecureData($transaction, $data);

        $this->processFraudCheckData($transaction, $data);
    }

    /**
     * Process callback
     *
     * @param Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    public function processCallback(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $request = Request::getInstance();

        if (
            !$request->txnId
            || !XPaymentsClient::getInstance()->getUpdateData()
        ) {
            // This is not X-Payments callback. We should not be here actually
            if (class_exists(CallbackRequestError::class)) {
                throw new CallbackRequestError('Not an X-Payments callback.');
            } else {
                $this->markCallbackRequestAsInvalid(static::t('Not an X-Payments callback.'));
                return;
            }
        }

        if (
            $transaction->getXpcDataCell('xpc_deny_callbacks')
            && intval($transaction->getXpcDataCell('xpc_deny_callbacks')->getValue())
            && version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.9') < 0
        ) {
            if (class_exists(CallbackNotReady::class)) {
                throw new CallbackNotReady();
            } else {
                $this->markCallbackRequestAsInvalid(static::t('X-Payments callback received before order placement. Skipped.'));
                return;
            }
        }

        $updateData = XPaymentsClient::getInstance()->getUpdateData();

        if (isset($updateData['status'])) {
            $status = $this->getTransactionStatus($updateData, $transaction);
            $transaction->setStatus($status);
            $this->setTransactionTypeByStatus($transaction, $updateData['status']);
            $this->registerBackendTransaction($transaction, $updateData);
        }

        $this->processTransactionUpdate($transaction, $updateData, $request->txnId);

        Database::getEM()->flush();
    }

    /**
     * Get callback request owner transaction or null
     *
     * @return Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        if ($this->detectedTransaction) {
            return $this->detectedTransaction;
        }

        $request = Request::getInstance();

        if (
            !$request->xpcBackReference
            || !$request->txnId
        ) {
            // This is not X-Payments callback
            return null;
        }

        $logMessage = $parentId = $updateData = '';

        do {

            // Search transaction by back reference from request
            $transaction = $this->searchTransactionByBackReference($request->xpcBackReference);

            if (!$transaction) {
                $logMessage = 'Cannot detect transaction';
                $this->detectedTransaction = null; 
                break;
            }

            if ($request->action == 'check_cart') {
                // This is check cart callback request
                $logMessage = 'Check cart callback request received';
                $this->detectedTransaction = $transaction;
                break;
            } 

            try {

                // Extract decrypted data into array (exception can be thrown here)
                $updateData = $this->client->processApiResponse($request->updateData);

            } catch (\Qualiteam\SkinActXPaymentsConnector\Core\XpcResponseException $exception) {
                    
                // Error extracting the data from callback request
                $logMessage = $exception->getMessage();
                $this->detectedTransaction = null;
                break;
            }

            if (!isset($updateData['parentId'])) {
                // Parent ID is not received
                // This is calback of X-Payments payment method
                $logMessage = 'Payment update callback request';
                $this->detectedTransaction = $transaction;
                break;
            }

            $parentId = $updateData['parentId'];
            
            if ($this->isSavedCardsPaymentMethod()) {

                if (
                   $transaction->getXpcDataCell('is_recharge')
                   && 'Y' == $transaction->getXpcDataCell('is_recharge')->getValue()
                ) {
                    // This transaction is recharge for AOM
                    $logMessage = 'Recharge transaction for AOM found';
                    $this->detectedTransaction = $transaction;

                } else {

                    // Search Saved Card transaction in profile.
                    // Order is now being placed by customer.
                    $this->detectedTransaction = $this->searchSavedCardTransaction($transaction, $logMessage);
                }

                break;
            }

            if (!$this->isSavedCardTransaction($transaction)) {

                // Try to find a transaction with the same txnId received from X-Payments.
                // If it exists, it means that it was created by recharge callback from X-Payments.
                // And this request is a secondary action: capture/void/refund
                $secondaryTransactionForExtrnalRecharge = $this->searchTransactionByTxnId($request->txnId);

                if ($secondaryTransactionForExtrnalRecharge) {

                    // Use the already existing transaction
                    $this->detectedTransaction = $secondaryTransactionForExtrnalRecharge;
                    $logMessage = 'Found a transaction for which this is a secondary action';

                } else {
                    // Create new order for subscription/recharge created on X-Payments side
                    // by the original card present transaction passed by backreference
                    $this->detectedTransaction = $this->createChildTransaction($transaction, $updateData);
                    $logMessage = 'Create new transaction in the order callback request';
                }

                break; // Just in case
            }

        } while (false); // So it can in break 

        if ($this->detectedTransaction) {

            $logMessage = $logMessage . PHP_EOL
                . 'Callback Backreference: ' . var_export($request->xpcBackReference, true) . PHP_EOL
                . 'Callback txnId: ' . var_export($request->txnId, true) . PHP_EOL
                . 'Transaction ID: ' . ($transaction ? $this->detectedTransaction->getTransactionId() : 'n/a') . PHP_EOL
                . 'Parent ID: ' . ($parentId ? $parentId : 'n/a') . PHP_EOL
                . 'Data: ' . ($updateData ? var_export($updateData, true): 'n/a');

        }

        if ($logMessage) {
            $this->getLogger('Qualiteam-SkinActXPaymentsConnector')->error($logMessage);
        }

        return $this->detectedTransaction;
    }

    /**
     * Get return request owner transaction or null
     *
     * @return Transaction|void
     */
    public function getReturnOwnerTransaction()
    {
        $result = null;

        $xpcBackReference = Request::getInstance()->xpcBackReference;

        if ($xpcBackReference) {

            $result = $this->searchTransactionByBackReference($xpcBackReference);

            if (!$result) {
                // Hack against broken GET-parameter
                $xpcBackReference = substr($xpcBackReference, 0, 32);

                $result = $this->searchTransactionByBackReference($xpcBackReference);
            }
        }

        return $result;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new XPaymentsClient;
    }

    /**
     * Set initial payment transaction status by response status
     *
     * @param Transaction $transaction Transaction to update
     * @param integer $responseStatus Transaction status from X-Payments
     *
     * @return void
     */
    protected function setTransactionTypeByStatus(Transaction $transaction, $responseStatus)
    {
        // Initial transaction type is not known currently before payment, try to guess it from X-P transaction status
        // TODO: once API can pass initial transaction type, implement it in getInitialTransactionType()
        if (static::STATUS_AUTH == $responseStatus) {
            $transaction->setType(BackendTransaction::TRAN_TYPE_AUTH);
        } elseif (static::STATUS_CHARGED == $responseStatus) {
            $transaction->setType(BackendTransaction::TRAN_TYPE_SALE);
        }
    }

    /**
     * Get transaction status by action
     *
     * @param array $data Data
     *
     * @return string
     */
    protected function getTransactionStatus(array $data, Transaction $transaction)
    {
        switch (intval($data['status'])) {
            case static::STATUS_NEW:
                $transactionStatus = Transaction::STATUS_INITIALIZED;
                break;

            case static::STATUS_AUTH:
            case static::STATUS_CHARGED:
                $transactionStatus = Transaction::STATUS_SUCCESS;
                break;

            case static::STATUS_DECLINED:

                // Check if fraud status is not set in the received data.
                // In the transactions declined by admin in X-Payments
                // this flag is not set already.
                $fraudStatusInDataIsNotSet = !isset($data['isFraudStatus'])
                    || empty($data['isFraudStatus']);

                if (
                    $transaction->getXpcDataCell('xpc_is_fraud_status')
                    && 1 == $transaction->getXpcDataCell('xpc_is_fraud_status')->getValue()
                    && $fraudStatusInDataIsNotSet
                ) {
                    // This is a decline of fraudulent transaction made by X-Payments admin
                    $transactionStatus = Transaction::STATUS_CANCELED;
                } else {
                    $transactionStatus = (0 == $data['authorized'])
                        ? Transaction::STATUS_FAILED
                        : Transaction::STATUS_VOID;
                }

                break;

            case static::STATUS_REFUND:
            case static::STATUS_REFUND_PART:
            default:
                $transactionStatus = Transaction::STATUS_CANCELED;
                break;
        }

        return $transactionStatus;
    }

    /**
     * Get transaction refunded amount
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array                            $data        Data
     *
     * @return void
     */
    protected function getRefundedAmount(Transaction $transaction, array $data)
    {
        $amount = 0;
        $types = array(
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_REFUND_PART,
        );

        if (in_array($data['status'], array(static::STATUS_REFUND, static::STATUS_REFUND_PART))) {

            foreach ($transaction->getBackendTransactions() as $bt) {
                if ($bt->isCompleted() && in_array($bt->getType(), $types)) {
                    $amount += $bt->getValue();
                }
            }

            $amount = $data['refundedAmount'] - $amount;
        }

        return $amount;
    }

    /**
     * Register backend transaction
     *
     * @param Transaction $transaction Callback-owner transaction
     * @param array                            $data        Data
     *
     * @return void
     */
    protected function registerBackendTransaction(Transaction $transaction, array $data)
    {
        $type = null;
        $value = null;

        switch ($data['status']) {
            case static::STATUS_NEW:
                $type = BackendTransaction::TRAN_TYPE_SALE;
                break;

            case static::STATUS_AUTH:
                $type = BackendTransaction::TRAN_TYPE_AUTH;
                break;

            case static::STATUS_DECLINED:
                if (0 == $data['authorized'] && 0 == $data['chargedAmount']) {
                    $type = BackendTransaction::TRAN_TYPE_DECLINE;

                } elseif ($data['amount'] == $data['voidedAmount']) {
                    $type = BackendTransaction::TRAN_TYPE_VOID;

                } else {

                    $type = BackendTransaction::TRAN_TYPE_VOID_PART;
                    $value = $data['voidedAmount'];
                }

                break;

            case static::STATUS_CHARGED:
                if ($data['amount'] == $data['chargedAmount']) {
                    $type = BackendTransaction::TRAN_TYPE_CAPTURE;
                } else {
                    $type = BackendTransaction::TRAN_TYPE_CAPTURE_PART;
                    $value = $data['capturedAmount'];
                }
                break;

            case static::STATUS_REFUND:
                $type = BackendTransaction::TRAN_TYPE_REFUND;
                $value = $this->getRefundedAmount($transaction, $data);

                break;

            case static::STATUS_REFUND_PART:
                $type = BackendTransaction::TRAN_TYPE_REFUND_PART;
                $value = $this->getRefundedAmount($transaction, $data);

                break;


            default:

        }

        if ($type) {
            $backendTransaction = $transaction->createBackendTransaction($type);
            $backendTransaction->setStatus(BackendTransaction::STATUS_SUCCESS);
            if ($value) {
                $backendTransaction->setValue($value);
            }
            $backendTransaction->setDataCell('xpc_message', $data['message']);
            $backendTransaction->registerTransactionInOrderHistory('callback');
        }
    }

    /**
     * Search saved card transaction
     *
     * @param Transaction $transaction    Transaction
     * @param string                           $logMessage Mesage for log
     *
     * @return mixed
     */
    protected function searchSavedCardTransaction(Transaction $transaction, &$logMessage)
    {
        $result = null;

        $openProfileTransaction = $this->findOpenProfileTransaction($transaction);

        if ($openProfileTransaction) {

            // This is calback for open SavedCard transaction
            $logMessage = 'Open SavedCard transaction callback request';
            $result = $openProfileTransaction;

        } else {

            // Search transaction by txnId which is X-Payments' paymentId
            $initialTransaction = $this->searchTransactionByTxnId(Request::getInstance()->txnId);

            if ($initialTransaction) {

                // Return initial transaction found bt txnId
                // Applies for customers' transactions and recharges from X-Payments
                $logMessage = 'Secondary saved card transaction callback request';
                $result = $initialTransaction;
            }
        }

        return $result;
    }

    /**
     * Find trannsaction by X-Payments back refernece to X-Cart
     *
     * @param string $xpcBackReference X-Payment connector Backend reference
     *
     * @return Transaction
     */
    protected function searchTransactionByBackReference($xpcBackReference)
    {
        $transactionData = Database::getRepo(TransactionData::class)
            ->findOneBy(array('value' => $xpcBackReference, 'name' => 'xpcBackReference'));

        return $transactionData
            ? $transactionData->getTransaction()
            : null;
    }

    /**
     * Find trannsaction by X-Payments paymentId (stored as xpc_txnid)
     *
     * @param integer $xpcTxnId XPC transaction id
     *
     * @return Transaction
     */
    protected function searchTransactionByTxnId($xpcTxnId)
    {
        $transactionData = Database::getRepo(TransactionData::class)
            ->findOneBy(array('value' => $xpcTxnId, 'name' => 'xpc_txnid'));

        return $transactionData
            ? $transactionData->getTransaction()
            : null;
    }


    /**
     * Get Saved Card payment method
     *
     * @return Method
     */
    protected function getSavedCardsPaymentMethod()
    {
        return XPaymentsClient::getInstance()->fixSavedCardMethod();
    }

    /**
     * Is transaction payment method saved card or not
     *
     * @param Transaction $transaction Transaction
     *
     * @return boolean
     */
    protected function isSavedCardTransaction(Transaction $transaction)
    {
        return static::METHOD_SAVED_CARD == $transaction->getPaymentMethod()->getClass();
    }

    /**
     * Copy masked card number from one transaction to another
     *
     * @param Transaction $parentTransaction Parent transaction
     * @param Transaction $transaction New transaction
     *
     * @return void 
     */
    protected function copyMaskedCard($parentTransaction, $transaction)
    {
        $creditCardData = $parentTransaction->getCard();

        $transaction->saveCard(
            substr($creditCardData['card_number'], 0, 6), 
            substr($creditCardData['card_number'], -4),
            $creditCardData['card_type'],
            substr($creditCardData['expire'], 0, 2),
            substr($creditCardData['expire'], -4)
        );
    }

    /**
     * Create transaction by parent one and data passed from X-Payments
     *
     * @param Transaction $parentTransaction Parent transaction
     * @param array                            $updateData        Update data passed from X-PAyments
     *
     * @return Transaction
     */
    protected function createChildTransaction($parentTransaction, array $updateData)
    {
        $parentOrder = $parentTransaction->getOrder();

        $cart = $this->client->createFakeCart(
            $parentOrder->getOrigProfile(),
            $this->getSavedCardsPaymentMethod(),
            $updateData['amount'],
            'Payment from X-Payments',
            'X-Payments-payment'
        );

        $cart->setOrigProfile($parentOrder->getOrigProfile());

        $transaction = $cart->getFirstOpenPaymentTransaction();

        if ($transaction) {

            $transaction->setDataCell(
                'xpc_txnid',
                Request::getInstance()->txnId,
                'X-Payments transaction id'
            );
            $transaction->setDataCell('xpc_message', $updateData['message'], 'X-Payments response');

            $this->copyMaskedCard($parentTransaction, $transaction);
  
            $parentTransaction->getPaymentMethod()->getProcessor()->savePaymentSettingsToTransaction($transaction, $parentTransaction);
        }

        return $transaction;
    }

    /**
     * Find open transaction in customer's profile
     *
     * @param Transaction $parentTransaction Parent transaction
     *
     * @return Transaction
     */
    protected function findOpenProfileTransaction(Transaction $parentTransaction)
    {
        $openTransaction = null;

        $login = $parentTransaction->getOrder()->getProfile()->getLogin();

        $cnd = new CommonCell();

        $class = XpcTransactionData::class;

        $cnd->{\Qualiteam\SkinActXPaymentsConnector\Model\Repo\Payment\XpcTransactionData::SEARCH_LOGIN} = $login;

        $profileTransactions = Database::getRepo($class)->search($cnd);

        foreach ($profileTransactions as $transactionData) {

            $transaction = $transactionData->getTransaction();

            if (
                $this->isSavedCardTransaction($transaction)
                && ($transaction->isPending() || $transaction->isInProgress())
            ) {
                $openTransaction = $transaction;
                break;
            }
        }

        return $openTransaction;
    }

    /**
     * Get allowed backend transactions
     *
     * @return array
     */
    public function getAllowedTransactions()
    {
        return array(
            BackendTransaction::TRAN_TYPE_CAPTURE,
            BackendTransaction::TRAN_TYPE_VOID,
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_ACCEPT,
            BackendTransaction::TRAN_TYPE_DECLINE,
        );
    }

    /**
     * Do Capture, Void or Refund request.
     * Returns false on failure or redirects admin back to the order page
     * the necessary actions with the backend transaction are taken in the 
     * Callback request processing
     *
     * @param BackendTransaction $transaction Trandaction
     * @param string $paymentAction Payment action
     *
     * @return boolean
     */
    protected function doSecondary(BackendTransaction $transaction, $paymentAction)
    {
        if (!array_key_exists($paymentAction, $this->secondaryActionsMap)) {
            return false;
        }
        if (Request::getInstance()->trn_id) {
            $pt = Database::getRepo(Transaction::class)->find(Request::getInstance()->trn_id);
        } else {
            $pt = $transaction->getPaymentTransaction();
        }

        $method = $this->secondaryActionsMap[$paymentAction]['method'];
        $action = $this->secondaryActionsMap[$paymentAction]['action'];

        $txnId = $pt->getDataCell('xpc_txnid')->getValue();

        $result = $this->client->$method(
            $txnId,
            $pt,
            Request::getInstance()->amount
        );

        $isTransactionSuccess = false;

        if ($result->isSuccess()) {

            $response = $result->getResponse();

            if ('1' == $response['status']) {

                TopMessage::getInstance()->addInfo(
                    $response['message']
                        ? 'Transaction was ' . $action . ' with message: ' . $response['message']
                        : 'Operation successful'
                );

                $this->processTransactionUpdate($pt, $response, $txnId);
                $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                $isTransactionSuccess = true;

            } else {

                if ($response['error']) {
                    $message = 'Transaction was not ' . $action  . ' with error: ' . $response['error'];
                } elseif ($response['message']) {
                    $message = 'Transaction was not ' . $action  . ' with message: ' . $response['message'];
                } else {
                    $message = 'Operation failed';
                }

                TopMessage::getInstance()->addError($message);
            }

        } else {
            
            $message = 'Operation failed';

            if ($result->getError()) {
                $message .= '. ' . $result->getError();
            }

            TopMessage::getInstance()->addError($message);

            $transaction->setStatus(BackendTransaction::STATUS_FAILED);

        }

        return $isTransactionSuccess;
    }

    /**
     * Do 'CAPTURE' request on Authorized transaction.
     * Returns true on success or false on failure
     *
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doCapture(BackendTransaction $transaction)
    {
        return $this->doSecondary($transaction, 'capture');
    }

    /**
     * Do 'VOID' request on Authorized transaction.
     * Returns true on success or false on failure
     *
     * @param BackendTransaction $transaction Trandaction
     *
     * @return boolean
     */
    protected function doVoid(BackendTransaction $transaction)
    {
        $result = $this->doSecondary($transaction, 'void');
        if ($result) {
            $transaction->getPaymentTransaction()->setStatus(Transaction::STATUS_VOID);
        }
        return $result;
    }

    /**
     * Do 'REFUND' request on Captured/Saled transaction.
     * Returns true on success or false on failure
     *
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefund(BackendTransaction $transaction)
    {
        return $this->doSecondary($transaction, 'refund');
    }

    /**
     * Do 'ACCEPT' request on Fraud transaction.
     * Returns true on success or false on failure
     *
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doAccept(BackendTransaction $transaction)
    {
        return $this->doSecondary($transaction, 'accept');
    }

    /**
     * Do 'DECLINE' request on Fraud transaction.
     * Returns true on success or false on failure
     *
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doDecline(BackendTransaction $transaction)
    {
        return $this->doSecondary($transaction, 'decline');
    }

    /**
     * Do Recharge the difference request.
     * Returns false on failure or redirects admin back to the order page
     * the necessary actions with the backend transaction are taken in the
     * Callback request processing
     *
     * @param Order $order Order which is recharged
     * @param Transaction $parentCardTransaction Transaction with saved card
     * @param float $amount Amount to recharge
     * @param boolean $sendCart Send or not cart in request OPTIONAL
     *
     * @return boolean
     */
    public function doRecharge(Order $order, Transaction $parentCardTransaction, $amount, $sendCart = true)
    {
        $newTransaction = new Transaction;

        $newTransaction->setPaymentMethod($this->getSavedCardsPaymentMethod());
        $newTransaction->setStatus(Transaction::STATUS_INPROGRESS);

        $newTransaction->setXpcDataCell('is_recharge', 'Y');
        $newTransaction->setValue($amount);

        $order->addPaymentTransactions($newTransaction);
        $newTransaction->setOrder($order);

        foreach ($this->paymentSettingsToSave as $field) {

            $key = 'xpc_can_do_' . $field;
            if (
                $parentCardTransaction->getXpcDataCell($key)
                && $parentCardTransaction->getXpcDataCell($key)->getValue()
            ) {
                $newTransaction->setXpcDataCell($key, $parentCardTransaction->getXpcDataCell($key)->getValue());
            }
        }


        $this->copyMaskedCard($parentCardTransaction, $newTransaction);

        $recharge = $this->client->requestPaymentRecharge(
            $parentCardTransaction->getDataCell('xpc_txnid')->getValue(),
            $newTransaction,
            null,
            $sendCart
        );

        if ($recharge->isSuccess()) {

            // Update masked card data
            $this->copyMaskedCard($newTransaction, $parentCardTransaction);

            $response = $recharge->getResponse();

            if ($response['transaction_id']) {
                $txnId = $response['transaction_id'];
                $newTransaction->setDataCell('xpc_txnid', $txnId, 'X-Payments transaction ID', 'C');
                $this->processTransactionUpdate($newTransaction, $response, $txnId);
            }

            if (
                self::STATUS_AUTH == $response['status']
                || self::STATUS_CHARGED == $response['status']
            ) {

                TopMessage::getInstance()->addInfo(
                    $response['message']
                        ? $response['message']
                        : 'Operation successful'
                );

                $newTransaction->setStatus(Transaction::STATUS_SUCCESS);
                $this->setTransactionTypeByStatus($newTransaction, $response['status']);

            } else {

                TopMessage::getInstance()->addError(
                    $response['error']
                        ? $response['error']
                        : 'Operation failed'
                );

                $newTransaction->setStatus(Transaction::STATUS_FAILED);
            }

            $newTransaction->setXpcDataCell('xpc_deny_callbacks', '0');

        } else {

            $message = 'Operation failed';

            if ($recharge->getError()) {
                $message .= '. ' . $recharge->getError();
            }

            TopMessage::getInstance()->addError($message);

            $newTransaction->setStatus(Transaction::STATUS_FAILED);
        }

        Database::getEM()->flush();
    }

    /**
     * Return true if backend transaction is allowed for current payment transaction
     *
     * @param Transaction $transaction     Payment transaction object
     * @param string                           $transactionType Backend transaction type
     *
     * @return boolean
     */
    public function isTransactionAllowed(Transaction $transaction, $transactionType)
    {
        $result = false;

        if (Request::getInstance()->trn_id) {
            $transaction = Database::getRepo(Transaction::class)->find(Request::getInstance()->trn_id);
        }

        if (BackendTransaction::TRAN_TYPE_CAPTURE == $transactionType) {

            if (
                $transaction->getXpcDataCell('xpc_can_do_capture')
                && $transaction->getXpcDataCell('xpc_can_capture')
            ) {
                $result = ($transaction->getXpcDataCell('xpc_can_capture')->getValue() > 0);
            }

        } elseif (BackendTransaction::TRAN_TYPE_VOID == $transactionType) {

            if (
                $transaction->getXpcDataCell('xpc_can_do_void')
                && $transaction->getXpcDataCell('xpc_can_void')
            ) {
                $result = ($transaction->getXpcDataCell('xpc_can_void')->getValue() > 0);
            }

        } elseif (BackendTransaction::TRAN_TYPE_REFUND == $transactionType) {

            if (
                $transaction->getXpcDataCell('xpc_can_do_refund')
                && $transaction->getXpcDataCell('xpc_can_refund')
            ) {
                $result = ($transaction->getXpcDataCell('xpc_can_refund')->getValue() > 0);
            }

        } elseif (
            BackendTransaction::TRAN_TYPE_ACCEPT == $transactionType
            || BackendTransaction::TRAN_TYPE_DECLINE == $transactionType
        ) {

            if (
                $transaction->getXpcDataCell('xpc_is_fraud_status')
                && $transaction->getXpcDataCell('xpc_is_fraud_status')->getValue()
            ) {
                $result = true;
            }
        }

        return $result;
    }
}
