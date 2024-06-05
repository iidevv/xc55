<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Payment;

use StdClass;
use XLite;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\InjectLoggerTrait;
use XC\Stripe\Main;
use XLite\Model\Country;
use XLite\Model\Order;
use XLite\Model\Shipping\Markup;

/**
 * Stripe payment processor
 */
class Stripe extends \XC\Stripe\Model\Payment\AStripe
{
    use InjectLoggerTrait;

    // The list of payment methods that are supported in X-Cart.
    protected const PAYMENT_METHODS = [
        'card'              => [
            'title' => 'Credit card',
        ],
        'afterpay_clearpay' => [
            'title'      => 'Afterpay and Clearpay',
            'currencies' => ['USD', 'CAD', 'GBP', 'AUD', 'NZD', 'EUR'],
        ],
        'alipay'            => [
            'title'      => 'Alipay',
            'currencies' => ['CNY', 'AUD', 'CAD', 'EUR', 'GBP', 'HKD', 'JPY', 'SGD', 'MYR', 'NZD', 'USD'],
        ],
        'bancontact'        => [
            'title'      => 'Bancontact',
            'currencies' => ['EUR'],
        ],
        'boleto'            => [
            'title'      => 'Boleto',
            'currencies' => ['BRL'],
            'delayed'    => true,
        ],
        'blik'              => [
            'title'      => 'BLIK',
            'currencies' => ['PLN'],
        ],
        'eps'               => [
            'title'      => 'EPS',
            'currencies' => ['EUR'],
        ],
        'fpx'               => [
            'title'      => 'FPX',
            'currencies' => ['MYR'],
        ],
        'giropay'           => [
            'title'      => 'GiroPay',
            'currencies' => ['EUR'],
        ],
        'grabpay'           => [
            'title'      => 'GrabPay',
            'currencies' => ['SGD', 'MYR'],
        ],
        'ideal'             => [
            'title'      => 'iDeal',
            'currencies' => ['EUR'],
        ],
        'konbini'           => [
            'title'      => 'Konbini',
            'currencies' => ['JPY'],
            'delayed'    => true,
        ],
        'klarna'            => [
            'title'      => 'Klarna',
            'currencies' => ['EUR', 'USD', 'GBP', 'DKK', 'SEK', 'NOK'],
        ],
        'oxxo'              => [
            'title'      => 'OXXO',
            'currencies' => ['MXN'],
            'delayed'    => true,
        ],
        'paynow'            => [
            'title'      => 'Pay now',
            'currencies' => ['SGD'],
        ],
        'pix'               => [
            'title'      => 'Pix',
            'currencies' => ['BRL'],
        ],
        'promptpay'         => [
            'title'      => 'PromptPay',
            'currencies' => ['THB'],
        ],
        'p24'               => [
            'title'      => 'Przelewy24',
            'currencies' => ['EUR', 'PLN'],
        ],
        'sepa_debit'        => [
            'title'      => 'Sepa',
            'currencies' => ['EUR'],
        ],
        'sofort'            => [
            'title'      => 'Sofort',
            'currencies' => ['EUR'],
        ],
        'wechat_pay'        => [
            'title'      => 'WeChat Pay',
            'currencies' => ['CNY', 'AUD', 'CAD', 'EUR', 'GBP', 'HKD', 'JPY', 'SGD', 'USD', 'DKK', 'NOK', 'SEK', 'CHF'],
        ],
    ];

    /*
     * Countries that might be passed in the list of countries available for shipping when Afterpay/Clearpay is enabled
     * and there is impossible to gather the full shipping address from the order (e.g. not all the necessary address
     * fields are present). The list might differ from the X-Cart countries list, and only the countries from this list
     * are allowed.
     */
    protected const ALLOWED_COUNTRIES = [
        'AC', 'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AT', 'AU', 'AW', 'AX', 'AZ', 'BA', 'BB',
        'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BQ', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY',
        'BZ', 'CA', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CV', 'CW', 'CY', 'CZ', 'DE',
        'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FO', 'FR', 'GA',
        'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY',
        'HK', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IS', 'IT', 'JE', 'JM', 'JO', 'JP',
        'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT',
        'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MF', 'MG', 'MK', 'ML', 'MM', 'MN', 'MO', 'MQ', 'MR', 'MS', 'MT',
        'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM',
        'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU',
        'RW', 'SA', 'SB', 'SC', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV',
        'SX', 'SZ', 'TA', 'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TW',
        'TZ', 'UA', 'UG', 'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VN', 'VU', 'WF', 'WS', 'XK', 'YE', 'YT', 'ZA',
        'ZM', 'ZW', 'ZZ'
    ];

    public const API_VERSION = '2020-08-27';

    /*
     * Store's currency will be cached there, as it is expected that some functions that need the store currency might
     * be called quite a times during a single request.
     */
    protected static $storeCurrency = null;

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getReferralPageURL(\XLite\Model\Payment\Method $method)
    {
        return '';
    }

    /**
     * Check - payment method connected to Stripe or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isSettingsConfigured(\XLite\Model\Payment\Method $method)
    {
        return ($method->getSetting('accessToken') && $method->getSetting('publishKey'))
            || ($method->getSetting('accessTokenTest') && $method->getSetting('publishKeyTest'));
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
            && !Main::getStripeConnectMethod()->getEnabled()
            && !empty($this->getEnabledPaymentMethods(null));
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
     * Get allowed backend transactions
     *
     * @return array Status codes
     */
    public function getAllowedTransactions()
    {
        return [
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XC\Stripe\View\StripeConfig';
    }

    /**
     * Return true if payment method settings form should use default submit button.
     * Otherwise, settings widget must define its own button
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return false;
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
     * Get additional payment options (required for some payment methods, such as WeChat Pay).
     *
     * @param array $paymentMethods The list of enabled payment methods.
     *
     * @return array
     */
    protected function getPaymentMethodOptions(array $paymentMethods): array
    {
        $result = [];
        if (in_array('wechat_pay', $paymentMethods, true)) {
            $result['wechat_pay'] = ['client' => 'web'];
        }

        return $result;
    }

    /**
     * Get payment intent data.
     *
     * @param array $paymentMethods The list of enabled payment methods.
     *
     * @return array
     */
    protected function getPaymentIntentData(array $paymentMethods): array
    {
        $result = [
            'capture_method' => $this->isCapture() ? 'automatic' : 'manual',
            'description'    => static::t('Payment transaction ID') . ': ' . $this->transaction->getPublicId(),
            'metadata'       => [
                'txnId' => $this->transaction->getPublicTxnId(),
            ],
        ];

        if (
            in_array('afterpay_clearpay', $paymentMethods, true)
            && $shippingInfo = $this->getShippingInfoForAfterpayClearpay()
        ) {
            $result['shipping'] = $shippingInfo;
        }

        return $result;
    }

    /**
     * Get shipping address collection. Required for Afterpay / Clearpay payment method.
     *
     * @param array $paymentMethods The list of enabled payment methods.
     * @param array $sessionParams  Stripe session params.
     *
     * @return array|null
     */
    protected function getShippingAddressCollection(array $paymentMethods, array $sessionParams): ?array
    {
        $result = null;

        /*
         * Afterpay / Clearpay requires shipping address OR a collection of countries allowed for shipping (NOT both).
         * If there is no shipping address in the Stripe session data, trying to add the list of countries which are
         * allowed for the selected shipping method.
         */
        if (
            in_array('afterpay_clearpay', $paymentMethods, true)
            && empty($sessionParams['payment_intent_data']['shipping'])
            && $shippingCountriesCollection = $this->getCountriesAvailableForShipping()
        ) {
            $result = [
                'allowed_countries' => $shippingCountriesCollection
            ];
        }

        return $result;
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

        $order = $this->getOrder();
        $paymentMethods = $this->getEnabledPaymentMethods($order);

        $params = [
            'success_url'            => $this->getReturnURL(null, true),
            'cancel_url'             => $this->getReturnURL(null, true, true),
            'mode'                   => 'payment',
            'payment_method_types'   => $paymentMethods,
            'payment_method_options' => $this->getPaymentMethodOptions($paymentMethods),
            'client_reference_id'    => $this->getOrder()->getOrderId(),
            'customer_email'         => $this->getProfile()->getLogin(),
            'line_items'             => $lineItems,
            'payment_intent_data'    => $this->getPaymentIntentData($paymentMethods),
        ];

        if ($shippingAddressCollection = $this->getShippingAddressCollection($paymentMethods, $params)) {
            $params['shipping_address_collection'] = $shippingAddressCollection;
        }

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
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
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

            $status   = \XLite\Model\Payment\Transaction::STATUS_FAILED;
            $btStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;
            $error    = '';
            if (in_array($intent->status, ['succeeded', 'requires_capture', 'processing'])) {
                if (
                    $intent->status === 'processing'
                    && ($transaction->isInProgress() || $transaction->isPending())
                ) {
                    $status   = \XLite\Model\Payment\Transaction::STATUS_PENDING;
                    $btStatus = \XLite\Model\Payment\BackendTransaction::STATUS_PENDING;
                } elseif ($intent->status !== 'processing') {
                    $status   = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;
                    $btStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;
                }
                $transaction->setNote('');

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

                /** @var \Stripe\Charge $charge */
                $charge = $intent->charges->first();
                if ($charge && $charge->failure_message) {
                    $error = $charge->failure_message;
                    $transaction->setNote($error);
                }
            }

            if ($error) {
                $status   = \XLite\Model\Payment\Transaction::STATUS_FAILED;
                $btStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;
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
                'exceptionMessage' => $e->getMessage()
            ]);
        }
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
                $method = $this->transaction->getPaymentMethod();
                $key    = $this->getActualClientSecret($method);
            } else {
                $method = Main::getStripeMethod();
                $key    = $this->getActualClientSecret($method);
            }

            \Stripe\Stripe::setApiKey($key);
            \Stripe\Stripe::setApiVersion(static::API_VERSION);

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
     * Capture
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDetail('stripe_id')
            );
            $paymentIntent->capture();

            if ($paymentIntent->status == 'succeeded') {
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                $this->getLogger('XC-Stripe')->debug('Success: ' . __FUNCTION__, [
                    'id'      => $paymentIntent->id,
                    'amount'  => $paymentIntent->amount,
                    'status'  => $paymentIntent->status,
                ]);
            }

            if (!empty($paymentIntent->charges->data)) {
                $charge = reset($paymentIntent->charges->data);
                $transaction->setDataCell('stripe_b_txntid', $charge->balance_transaction);
            }
        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            $this->getLogger('XC-Stripe')->debug(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);

        return $backendTransactionStatus == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;
    }

    /**
     * Void
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDetail('stripe_id')
            );
            $paymentIntent->cancel();

            if ($paymentIntent->status == 'canceled') {
                $charge = reset($paymentIntent->charges->data);
                if ($charge && $charge->refunds->data) {
                    $refundTransaction = reset($charge->refunds->data);

                    if ($refundTransaction) {
                        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                        $transaction->setDataCell('stripe_date', $refundTransaction->created);
                        if ($refundTransaction->balance_transaction) {
                            $transaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
                        }
                    }
                }

                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                $this->getLogger('XC-Stripe')->debug('Success: ' . __FUNCTION__, [
                    'id'      => $paymentIntent->id,
                    'amount'  => $paymentIntent->amount,
                    'status'  => $paymentIntent->status,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            $this->getLogger('XC-Stripe')->debug(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $paymentTransaction = $transaction->getPaymentTransaction();

        $transaction->setStatus($backendTransactionStatus);
        $paymentTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);

        return $backendTransactionStatus == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefundMulti(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDetail('stripe_id')
            );

            $payment = !empty($paymentIntent->charges->data)
                ? reset($paymentIntent->charges->data)
                : null;

            if (!$payment) {
                throw new \Exception('No charges found for payment intent ' . $paymentIntent->id);
            }

            if ($transaction->getValue() != $transaction->getPaymentTransaction()->getValue()) {
                $payment->refunds->create([
                    'amount' => $this->formatCurrency($transaction->getValue()),
                ]);

                /** @var \Stripe\Refund $refundTransaction */
                $refundTransaction = null;

                if ($payment->refunds) {
                    foreach ($payment->refunds->all() as $r) {
                        if (!$this->isRefundTransactionRegistered($r)) {
                            $refundTransaction = $r;
                            break;
                        }
                    }
                }
            } else {
                $refundTransaction = $payment->refunds->create();
            }

            if ($refundTransaction) {
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                $transaction->setDataCell('stripe_date', $refundTransaction->created);
                if ($refundTransaction->balance_transaction) {
                    $transaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
                }

                $this->getLogger('XC-Stripe')->debug('Success: ' . __FUNCTION__, [
                    'id'                  => $refundTransaction->id,
                    'amount'              => $refundTransaction->amount,
                    'balance_transaction' => $refundTransaction->balance_transaction,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            $this->getLogger('XC-Stripe')->debug(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);

        return $backendTransactionStatus == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;
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
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];

        foreach ($this->transaction->getBackendTransactions() as $bt) {
            $txnid = $bt->getDataCell('stripe_b_txntid');
            if (
                in_array($bt->getType(), $types)
                && (!$txnid || $txnid->getValue() == $refund->balance_transaction)
                && (
                    $bt->getDataCell('stripe_date')
                    && $bt->getDataCell('stripe_date')->getValue() == $refund->created
                )
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
     * @return \Stripe\Refund|StdClass|null
     */
    protected function getRefundObject($event)
    {
        if (!empty($event->data->object->refunds)) {
            $refunds = $event->data->object->refunds instanceof \Stripe\Collection
                ? $event->data->object->refunds->data
                : $event->data->object->refunds;

            foreach ($refunds as $r) {
                if (!$this->isRefundTransactionRegistered($r)) {
                    return $r;
                }
            }
        } elseif (
            !empty($event->data->object->amount_refunded)
            && isset($event->data->previous_attributes->amount_refunded)
        ) {
            $result = new StdClass();
            $result->amount = max(
                0,
                $event->data->object->amount_refunded - $event->data->previous_attributes->amount_refunded
            );
            $result->created = $event->created;

            return $result;
        }

        return null;
    }

    // }}}

    // {{{ Callback

    /**
     * Process event charge.refunded
     *
     * @param \Stripe\Event                    $event       Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargeRefunded($event, $transaction)
    {
        $refundTransaction = $this->getRefundObject($event);

        if (
            $refundTransaction
            && !$this->isBackendTransactionSuccessful(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND)
        ) {
            $amount = $this->transaction->getCurrency()->convertIntegerToFloat($refundTransaction->amount);

            if ($amount != $this->transaction->getValue()) {
                $backendTransaction = $this->registerBackendTransaction(
                    \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART
                );
                $backendTransaction->setValue($amount);
            } else {
                $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND;
                if (!$this->transaction->isCaptured()) {
                    $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID;
                    $this->transaction->setType($type);
                    $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);
                }
                $backendTransaction = $this->registerBackendTransaction($type);
            }

            $backendTransaction->setDataCell('stripe_date', $refundTransaction->created);
            if ($refundTransaction->balance_transaction) {
                $backendTransaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
            }

            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('callback');
        } elseif ($this->isBackendTransactionSuccessful(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND)) {
            $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND);
            $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
        } else {
            $this->getLogger('XC-Stripe')->debug('Duplicate charge.refunded event # ' . $event->id);
        }
    }

    /**
     * Process event charge.captured
     *
     * @param \Stripe\Event                    $event       Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargeCaptured($event, $transaction)
    {
        $refundTransaction = $this->getRefundObject($event);

        if (!$this->isBackendTransactionSuccessful(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE)) {
            $amount = $this->transaction->getValue();
            if ($refundTransaction) {
                $amountRefunded = $this->transaction->getCurrency()->convertIntegerToFloat($refundTransaction->amount);
                $amountFull     = $this->transaction->getCurrency()->convertIntegerToFloat($event->data->object->amount);
                $amount         = $amountFull - $amountRefunded;
                if ($amount != $this->transaction->getValue()) {
                    $backendTransaction = $this->registerBackendTransaction(
                        \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI
                    );
                    $backendTransaction->setValue($amountRefunded);
                    $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
                    $backendTransaction->registerTransactionInOrderHistory('callback');
                }
            }

            $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE;
            if ($refundTransaction) {
                $type               = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART;
                $backendTransaction = $this->registerBackendTransaction($type);
                $backendTransaction->setValue($amount);

                $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART);
                $this->transaction->setValue($amount);
                $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            } else {
                $backendTransaction = $this->registerBackendTransaction($type);
                $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE);
                $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            }
            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('callback');
        } else {
            $this->getLogger('XC-Stripe')->debug('Duplicate charge.captured event # ' . $event->id);
        }
    }

    /**
     * Check if event is already handled
     *
     * @param string $type
     *
     * @return bool
     */
    protected function isBackendTransactionSuccessful($type)
    {
        foreach ($this->transaction->getBackendTransactions() as $bt) {
            if (
                $bt->getType() == $type
                && $bt->getStatus() == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS
            ) {
                return true;
            }
        }

        return false;
    }

    // }}}

    /**
     * Get all Stripe payment methods that are supported in X-Cart.
     */
    public static function getAllPaymentMethods(): array
    {
        return array_keys(self::PAYMENT_METHODS);
    }

    /**
     * Payment methods that are enabled by default.
     */
    public static function getDefaultPaymentMethodsEnabled(): array
    {
        return [static::getAllPaymentMethods()[0]];
    }

    /**
     * Get only the payment methods that are available for the currency that the store uses.
     *
     * @param array $methods Stripe payment methods to filter.
     */
    public static function filterAvailablePaymentMethods(array $methods): array
    {
        return array_values(
            array_filter(
                $methods,
                static fn(string $method) => static::isPaymentMethodAvailable($method)
            )
        );
    }

    /**
     * Get all payment methods that are available for the currency the store uses.
     */
    public static function getAvailablePaymentMethods(): array
    {
        $availablePaymentMethods = static::getAllPaymentMethods();
        $result = [];
        foreach ($availablePaymentMethods as $method) {
            $result[$method] = static::t(static::PAYMENT_METHODS[$method]['title']);
        }

        return $result;
    }

    /**
     * Get the store currency code. It's cached because sometimes this function is meant to be called quite often
     * within a single request.
     */
    protected static function getStoreCurrencyCode(): string
    {
        if (!static::$storeCurrency) {
            static::$storeCurrency = XLite::getInstance()->getCurrency()->getCode();
        }

        return (string) static::$storeCurrency;
    }

    /**
     * Whether the current Stripe payment method exists.
     *
     * @param string $method Method's service name.
     */
    public static function isPaymentMethodExist(string $method): bool
    {
        return in_array($method, static::getAllPaymentMethods(), true);
    }

    /**
     * Whether the current Stripe payment method is available.
     *
     * @param string $method Method's service name.
     */
    public static function isPaymentMethodAvailable(string $method): bool
    {
        if (!static::isPaymentMethodExist($method)) {
            return false;
        }
        $methodInfo = static::PAYMENT_METHODS[$method];

        return (
            !array_key_exists('currencies', $methodInfo)
            || in_array(static::getStoreCurrencyCode(), $methodInfo['currencies'], true)
        );
    }

    /**
     * Disable Konbini if it is enabled but cannot be used to pay for the order.
     *
     * @param array $methods The list of enabled payment methods.
     * @param Order $order   The order.
     *
     * @return array The array of methods with Konbini filtered out in case it cannot be used to pay for the order.
     */
    protected function maybeDisableKonbini(array $methods, Order $order): array
    {
        if (
            in_array('konbini', $methods, true)
            && $order->getCurrency()->getCode() === 'JPY'
            && !$this->isKonbiniAvailableForTotal($order->getTotal())
        ) {
            $methods = array_values(
                array_filter(
                    $methods,
                    static fn(string $method): bool => $method !== 'konbini'
                )
            );
        }

        return $methods;
    }

    /**
     * Disable Afterpay and Clearpay if it is enabled but cannot be used to pay for the order.
     *
     * @param array $methods The list of enabled payment methods.
     * @param Order $order   The order.
     *
     * @return array The array of methods with Afterpay and Clearpay filtered out in case it cannot be used to pay for
     *               the order.
     */
    protected function maybeDisableAfterpayClearpay(array $methods, Order $order): array
    {
        if (
            in_array('afterpay_clearpay', $methods, true)
            && !$this->getShippingInfoForAfterpayClearpay($order)
            && !$this->getCountriesAvailableForShipping($order)
        ) {
            $methods = array_values(
                array_filter(
                    $methods,
                    static fn(string $method): bool => $method !== 'afterpay_clearpay'
                )
            );
        }

        return $methods;
    }

    /**
     * Get all enabled Stripe payment methods. These are the payment methods that were enabled in the admin area
     * excluding the methods that doesn't support the store's currency or doesn't meet some additional criteria.
     *
     * @param Order|null $order Optional. Order. If not null, some additional checks will be performed.
     */
    public function getEnabledPaymentMethods($order = null): array
    {
        $method = Main::getStripeMethod();
        if ($paymentMethods = $method->getSetting('payment_methods')) {
            $paymentMethods = json_decode($paymentMethods, true);
            if (is_array($paymentMethods)) {
                if ($order instanceof Order) {
                    $methods = $this->maybeDisableAfterpayClearpay(
                        $this->maybeDisableKonbini(
                            static::filterAvailablePaymentMethods($paymentMethods),
                            $order
                        ),
                        $order
                    );
                } else {
                    $methods = $paymentMethods;
                }

                return $methods;
            }
        }

        return static::getDefaultPaymentMethodsEnabled();
    }

    /**
     * Whether the order is available for being paid via Konbini. The payment method doesn't accept payments less than
     * 120 yen and more than 300,000 yen.
     *
     * @param float $totalPrice Order total
     */
    public function isKonbiniAvailableForTotal(float $totalPrice): bool
    {
        return ($totalPrice >= 120.0 && $totalPrice <= 300000.0);
    }

    /**
     * Check whether the payment processor is applicable.
     *
     * @param Order                       $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(Order $order, \XLite\Model\Payment\Method $method)
    {
        return (
            $this->getEnabledPaymentMethods($order)
            && !(
                $order->getCurrency()->getCode() === 'JPY'
                && $order->getTotal() < 50.0 // Stripe doesn't accept payments less than 50 yen.
            )
            && parent::isApplicable($order, $method)
        );
    }

    /**
     * Get the list of countries that are available for the shipping via the selected shipping method (if any).
     *
     * @param Order|null $order Optional. Order instance. If null, {@see Stripe::getOrder()} will be used instead.
     */
    public function getCountriesAvailableForShipping($order = null): array
    {
        $result = [];
        $order = $order ?: $this->getOrder();
        $shippingMethod = Database::getRepo('XLite\Model\Shipping\Method')->find($order->getShippingId());
        if ($shippingMethod) {
            $markups = Database::getRepo('XLite\Model\Shipping\Markup')->findBy([
                'shipping_method' => $shippingMethod
            ]);
            if ($markups) {
                $countries = array_reduce(
                    $markups,
                    static function (array $carry, Markup $markup): array {
                        $zone = $markup->getZone();
                        if ($zone) {
                            $zoneCountries = $zone->getZoneCountries();
                            if (count($zoneCountries) === 0) {
                                $zoneCountries = Database::getRepo('XLite\Model\Country')->findAllCountries();
                            }

                            return array_merge(
                                $carry,
                                array_map(
                                    static fn(Country $country) => $country->getCode(),
                                    $zoneCountries
                                )
                            );
                        }

                        return $carry;
                    },
                    []
                );
                $result = array_values(
                    array_filter(
                        array_unique($countries),
                        static fn(string $country): bool => in_array($country, static::ALLOWED_COUNTRIES, true)
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Get shipping info that is passed to Stripe when Afterpay/Clearpay is enabled.
     *
     * @param Order|null $order Optional. Order instance. If null, {@see Stripe::getOrder()} will be used instead.
     *
     * @return array|null Shipping info if all the necessary address fields exist and aren't empty, null otherwise.
     */
    public function getShippingInfoForAfterpayClearpay($order = null): ?array
    {
        $result = null;
        $order = $order ?: $this->getOrder();
        if (
            $order
            && ($profile = $order->getProfile())
            && ($shippingAddress = $profile->getShippingAddress())
        ) {
            $shippingAddress = $shippingAddress->toArray();
            if (
                !empty($shippingAddress['name'])
                && !empty($shippingAddress['address'])
                && !empty($shippingAddress['country'])
                && !empty($shippingAddress['zipcode'])
            ) {
                $result = [
                    'name'    => $shippingAddress['name'],
                    'address' => [
                        'country'     => $shippingAddress['country'],
                        'line1'       => $shippingAddress['address'],
                        'postal_code' => $shippingAddress['zipcode'],
                    ]
                ];
                if (!empty($shippingAddress['custom_state'])) {
                    $result['address']['state'] = $shippingAddress['custom_state'];
                }
                if (!empty($shippingAddress['city'])) {
                    $result['address']['city'] = $shippingAddress['city'];
                }
            }
        }

        return $result;
    }

    protected function isDelayedPaymentMethod(string $method): bool
    {
        return !empty(static::PAYMENT_METHODS[$method]['delayed']);
    }

    protected function failTransaction($event, \XLite\Model\Payment\Transaction $transaction): void
    {
        if ($transaction->isPending() || $transaction->isInProgress()) {
            $transaction->setStatus(XLite\Model\Payment\BackendTransaction::STATUS_FAILED);
            $transaction->registerTransactionInOrderHistory('callback');
        }
    }

    protected function successTransaction($event, \XLite\Model\Payment\Transaction $transaction): void
    {
        if ($transaction->getStatus() !== XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS) {
            $transaction->setStatus(XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $transaction->registerTransactionInOrderHistory('callback');
        }
    }

    protected function processEventCheckoutSessionAsyncPaymentSucceeded(
        $event,
        \XLite\Model\Payment\Transaction $transaction
    ): void {
        $this->successTransaction($event, $transaction);
    }

    protected function processEventCheckoutSessionAsyncPaymentFailed(
        $event,
        \XLite\Model\Payment\Transaction $transaction
    ): void {
        $this->failTransaction($event, $transaction);
    }

    protected function processEventCheckoutSessionExpired($event, \XLite\Model\Payment\Transaction $transaction): void
    {
        $this->failTransaction($event, $transaction);
    }

    protected function processEventChargeFailed($event, \XLite\Model\Payment\Transaction $transaction): void
    {
        $this->failTransaction($event, $transaction);
    }

    protected function processEventChargeSucceeded($event, \XLite\Model\Payment\Transaction $transaction): void
    {
        $this->successTransaction($event, $transaction);
    }

    protected function processEventPaymentIntentRequiresAction(
        $event,
        \XLite\Model\Payment\Transaction $transaction
    ): void {
        if ($transaction->isInProgress()) {
            $paymentMethods = (array) ($event->data->object->payment_method_types ?? []);
            foreach ($paymentMethods as $method) {
                if ($this->isDelayedPaymentMethod($method)) {
                    $transaction->setStatus(XLite\Model\Payment\BackendTransaction::STATUS_PENDING);
                    $transaction->registerTransactionInOrderHistory('callback');
                    break;
                }
            }
        }
    }

    protected function canProcessCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        $realTransactionStatus = null;
        $realOrderNumber = null;
        $order = $transaction->getOrder();

        if (
            $transaction->isInProgress()
            && in_array($this->eventType, ['payment_intent.requires_action', 'checkout.session.expired'], true)
        ) {
            $realTransactionStatus = $transaction->getStatus();
            $realOrderNumber = $order->getOrderNumber();
            /*
             * parent::canProcessCallback checks whether the payment transaction is open or in process, so we need to
             * set its status to Pending to pass the check.
             */
            $transaction->setStatus(XLite\Model\Payment\BackendTransaction::STATUS_PENDING);
            if (!$realOrderNumber) {
                /*
                 * parent::canProcessCallback checks whether the order number exists, so we need any number
                 * to pass the check.
                 */
                $order->setOrderNumber('RND');
            }
        }

        $result = parent::canProcessCallback($transaction);

        if ($realTransactionStatus) {
            $transaction->setStatus($realTransactionStatus);
            $order->setOrderNumber($realOrderNumber);
        }

        return $result;
    }
}
