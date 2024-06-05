<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Payment;

use XLite\InjectLoggerTrait;
use XLite\Model\Payment\TransactionData;
use XLite\Model\Profile;

/**
 * Stripe payment processor
 */
abstract class AStripe extends \XLite\Model\Payment\Base\Online
{
    use InjectLoggerTrait;

    public const API_VERSION    = '2020-08-27';
    public const APP_NAME       = 'X-Cart Stripe plugin';
    public const APP_PARTNER_ID = 'pp_partner_DLMvmppc0YOIsZ';

    /**
     * Stripe library included flag
     *
     * @var boolean
     */
    protected $stripeLibIncluded = false;

    /**
     * Event id
     *
     * @var string
     */
    protected $eventId;

    /**
     * @var string
     */
    protected $eventType;

    /**
     * Get Webhook URL
     *
     * @return string
     */
    public function getWebhookURL()
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('callback', null, [], \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/XC/Stripe/payment.twig';
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
        $type = $method ? $method->getSetting('type') : $this->getSetting('type');

        return $type == 'sale'
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $this->includeStripeLibrary();
        $status = static::PROLONGATION;

        try {
            $session = \Stripe\Checkout\Session::create($this->getCheckoutSessionParams());

            $this->transaction->setDataCell('stripe_session_id', $session->id, null, TransactionData::ACCESS_CUSTOMER);
            $this->transaction->setDataCell('stripe_id', $session->payment_intent, null, TransactionData::ACCESS_CUSTOMER);

            $content = json_encode(['sessionId' => $session->id]);

            header('Content-Type: application/json; charset=UTF-8');
            header('Content-Length: ' . strlen($content));

            print ($content);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $status = static::FAILED;

            $this->getLogger('XC-Stripe')->debug('Create checkout session error: ' . $e->getMessage());
        }

        return $status;
    }

    /**
     * @param Profile $orderProfile
     * @param null    $stripeCustomerId
     *
     * @return \Stripe\Customer
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function updateStripeCustomer(Profile $orderProfile, $stripeCustomerId = null)
    {
        try {
            $stripeCustomer = null;
            if ($stripeCustomerId) {
                $stripeCustomer = \Stripe\Customer::retrieve($stripeCustomerId);

                if (!isset($stripeCustomer->id) || $stripeCustomer->isDeleted()) {
                    $stripeCustomer = null;
                }
            }

            $address      = $orderProfile->getBillingAddress();
            $customerData = [
                'email'   => $orderProfile->getLogin(),
                'name'    => $orderProfile->getName(),
                'address' => [
                    'city'        => $address->getCity(),
                    'country'     => $address->getCountryCode(),
                    'line1'       => $address->getStreet(),
                    'postal_code' => $address->getZipcode(),
                    'state'       => $address->getStateName(),
                ]
            ];

            if ($stripeCustomer) {
                if (!$this->checkStripeCustomerData($stripeCustomer, $customerData)) {
                    $stripeCustomer = \Stripe\Customer::update($stripeCustomer->id, $customerData);
                }
            } else {
                $stripeCustomer = \Stripe\Customer::create($customerData);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            static::log('Update/create stripe customer error: ' . $e->getMessage());
            $stripeCustomer = null;
        }

        return $stripeCustomer;
    }

    /**
     * Check if need update stripe customer data
     *
     * @param \Stripe\Customer $stripeCustomer
     * @param                  $customerData
     *
     * @return bool
     */
    protected function checkStripeCustomerData(\Stripe\Customer $stripeCustomer, $customerData): bool
    {
        $checkStripeObject = static function ($stripeObject, $data) use (&$checkStripeObject) {
            foreach ($data as $key => $datum) {
                if (is_array($datum)) {
                    if (!$checkStripeObject($stripeObject->{$key}, $datum)) {
                        return false;
                    }
                } else {
                    if ($stripeObject->{$key} !== $datum) {
                        return false;
                    }
                }
            }

            return true;
        };

        return $checkStripeObject($stripeCustomer, $customerData);
    }

    /**
     * Format currency
     *
     * @param float $value Currency value
     *
     * @return integer
     */
    protected function formatCurrency($value)
    {
        return $this->transaction->getCurrency()->roundValueAsInteger($value);
    }

    /**
     * Check - transaction is capture type or not
     *
     * @return boolean
     */
    protected function isCapture()
    {
        return $this->getInitialTransactionType() === \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Register backend transaction
     *
     * @param string                           $type        Backend transaction type OPTIONAL
     * @param \XLite\Model\Payment\Transaction $transaction Transaction OPTIONAL
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function registerBackendTransaction($type = null, \XLite\Model\Payment\Transaction $transaction = null)
    {
        if (!$transaction) {
            $transaction = $this->transaction;
        }

        if (!$type) {
            $type = $transaction->getType();
        }

        $backendTransaction = $transaction->createBackendTransaction($type);

        return $backendTransaction;
    }

    /**
     * Include Stripe library
     *
     * @return void
     */
    abstract protected function includeStripeLibrary();

    // {{{ Callback

    /**
     * Get callback owner transaction
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        $transaction = null;

        $eventId = $this->detectEventId();
        if ($eventId) {
            if ($eventType = $this->detectEventType()) {
                $method = $this->getEventHandlerMethodName($eventType);
                if (!method_exists($this, $method)) {
                    return null;
                }
            }

            $this->includeStripeLibrary();

            try {
                $event = \Stripe\Event::retrieve($eventId);
                if ($event) {
                    $transaction = null;
                    $repo        = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction');

                    switch ($event->data->object->object) {
                        case 'payment_intent':
                            $txnId       = $event->data->object->metadata->txnId;
                            $transaction = $repo->findOneBy(['publicTxnId' => $txnId]);
                            break;
                        case 'charge':
                        case 'checkout.session':
                            $intent      = \Stripe\PaymentIntent::retrieve($event->data->object->payment_intent);
                            $txnId       = $intent->metadata->txnId;
                            $transaction = $repo->findOneBy(['publicTxnId' => $txnId]);
                            break;
                        case 'transfer':
                            $transferId  = $event->data->object->id;
                            $transaction = $repo->findOneByCell('transfer_id', $transferId);
                            if (!$transaction) {
                                $repo        = \XLite\Core\Database::getRepo('\XLite\Model\Payment\BackendTransaction');
                                $bt          = $repo->scFindOneByCell('transfer_id', $transferId);
                                $transaction = $bt ? $bt->getPaymentTransaction() : null;
                            }
                            break;
                    }

                    if ($transaction) {
                        $this->eventId   = $eventId;
                        $this->eventType = $event->type;
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $transaction;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @throws \XLite\Core\Exception\PaymentProcessing\ACallbackException
     */
    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallback($transaction);

        if ($this->canProcessCallback($transaction)) {
            $this->processStripeEvent($transaction);

            // Remove ttl for IPN requests
            if ($transaction->isEntityLocked(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)) {
                $transaction->unsetEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
            }
        } else {
            throw new \XLite\Core\Exception\PaymentProcessing\CallbackNotReady();
        }
    }

    /**
     * @inheritdoc
     */
    public function processCallbackNotReady(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallbackNotReady($transaction);

        header('HTTP/1.1 409 Conflict', true, 409);
        header('Status: 409 Conflict');
        header('X-Robots-Tag: noindex, nofollow');
    }

    /**
     * Check if we can process IPN right now or should receive it later
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return boolean
     */
    protected function canProcessCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        return !$this->isIPNLocked($transaction) && $this->isOrderProcessed($transaction);
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return bool
     */
    protected function isIPNLocked(\XLite\Model\Payment\Transaction $transaction)
    {
        return $transaction->isEntityLocked(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)
            && !$transaction->isEntityLockExpired(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
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
        return !$transaction->isOpen() && !$transaction->isInProgress() && $transaction->getOrder()->getOrderNumber();
    }

    /**
     * doTransaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction object
     * @param string                           $transactionType Backend transaction type
     *
     * @return void
     */
    public function doTransaction(\XLite\Model\Payment\Transaction $transaction, $transactionType)
    {
        $transaction->setEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN, 300);

        parent::doTransaction($transaction, $transactionType);

        $transaction->unsetEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
    }

    /**
     * Process generic stripe event
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     */
    protected function processStripeEvent($transaction)
    {
        $this->includeStripeLibrary();

        try {
            $event = \Stripe\Event::retrieve($this->eventId);
            if ($event) {
                $name = $this->getEventHandlerMethodName($event->type);
                if (method_exists($this, $name)) {
                    // $name assembled from 'processEvent' + event type
                    $this->$name($event, $transaction);
                    \XLite\Core\Database::getEM()->flush();
                }

                $this->getLogger('XC-Stripe')->debug('Event handled: ' . $event->type . ' # ' . $this->eventId, [
                    'Processed' => (method_exists($this, $name) ? 'Yes' : 'No')
                ]);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param $eventName
     *
     * @return string
     */
    protected function getEventHandlerMethodName($eventName)
    {
        return 'processEvent' . \Includes\Utils\Converter::convertToUpperCamelCase($eventName);
    }

    /**
     * Detect event id
     *
     * @return string
     */
    protected function detectEventId()
    {
        $body  = @file_get_contents('php://input');
        $event = @json_decode($body);
        $id    = $event ? $event->id : null;

        return ($id && preg_match('/^evt_/Ss', $id)) ? $id : null;
    }

    /**
     * Detect event type
     *
     * @return string
     */
    protected function detectEventType()
    {
        $body  = @file_get_contents('php://input');
        $event = @json_decode($body);
        $type  = $event ? $event->type : null;

        return $type;
    }

    // }}}

    // {{{ Service requests

    /**
     * Retrieve acount
     *
     * @return \Stripe\Account
     */
    public function retrieveAcount()
    {
        $this->includeStripeLibrary();

        try {
            $account = \Stripe\Account::retrieve();
        } catch (\Exception $e) {
            $account = null;
        }

        return $account;
    }

    // }}}
}
