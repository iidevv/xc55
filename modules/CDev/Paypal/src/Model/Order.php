<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Called when an order successfully placed by a client
     */
    public function processSucceed()
    {
        if ($this->isPaypalMethod($this->getPaymentMethod())) {
            // Lock IPN processing for each transaction
            foreach ($this->getPaymentTransactions() as $transaction) {
                // Don't inline the $lock variable https://github.com/symfony/symfony/issues/32062#issuecomment-502681215
                // the code like `$transaction->createEntityAutoLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)->acquire();` won't work
                $lock = $transaction->createEntityAutoLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
                $lock->acquire();
            }
        }

        parent::processSucceed();
    }

    /**
     * Exclude Express Checkout from the list of available for checkout payment methods
     * if Payflow Link or Paypal Advanced are avavilable
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $list = parent::getPaymentMethods();
        $transaction = $this->getFirstOpenPaymentTransaction();
        $paymentMethod = $transaction ? $transaction->getPaymentMethod() : null;

        if (
            $paymentMethod === null
            || (!$this->isExpressCheckout($paymentMethod)
                && !$this->isPaypalCredit($paymentMethod)
            )
        ) {
            $expressCheckoutKey = null;
            $found = false;

            foreach ($list as $k => $method) {
                if ($this->isExpressCheckout($method)) {
                    $expressCheckoutKey = $k;
                }

                if (in_array($method->getServiceName(), ['PayflowLink', 'PaypalAdvanced'], true)) {
                    $found = true;
                }

                if ($expressCheckoutKey !== null && $found) {
                    break;
                }
            }

            if ($expressCheckoutKey !== null && $found) {
                unset($list[$expressCheckoutKey]);
            }
        }

        $list = $this->sortPaypalMethods($list);

        return $list;
    }

    /**
     * Get payment method name
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        $method = $this->getPaymentMethod();
        if ($method && $this->isPaypalCommercePlatform($method)) {
            $transaction = $this->getPaymentTransactions()->last();
            $fundingSource = $transaction->getDataCell('PaypalFundingSource');
            if ($fundingSource) {
                return $fundingSource->getValue() ?? $method->getTitle();
            }

            return $method->getTitle();
        }

        return parent::getPaymentMethodName();
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method|null
     */
    public function getPaymentMethod()
    {
        $method = parent::getPaymentMethod();

        if (!$method && count($this->getPaymentTransactions()) > 0) {
            $lastMethod = $this->getPaymentTransactions()->last() && $this->getPaymentTransactions()->last()->getPaymentMethod()
                ? $this->getPaymentTransactions()->last()->getPaymentMethod()
                : null;
            if (
                $lastMethod
                && ($this->isExpressCheckout($lastMethod)
                    || $this->isPaypalForMarketplaces($lastMethod)
                    || $this->isPaypalCredit($lastMethod))
            ) {
                $method = $lastMethod;
            }
        }

        return $method;
    }

    /**
     * Get only express checkout payment method
     *
     * @return array
     */
    public function getOnlyExpressCheckoutIfAvailable()
    {
        $list = parent::getPaymentMethods();

        $transaction = $this->getFirstOpenPaymentTransaction();

        $paymentMethod = $transaction ? $transaction->getPaymentMethod() : null;

        if (
            isset($paymentMethod)
            && ($this->isExpressCheckout($paymentMethod) || $this->isPaypalForMarketplaces($paymentMethod))
        ) {
            // If customer return from Express checkout to confirm payment
            $list = array_filter($list, function ($method) {
                return $this->isExpressCheckout($method) || $this->isPaypalForMarketplaces($method);
            });
        }

        return $list;
    }

    /**
     * Get only express checkout payment method
     *
     * @return array
     */
    public function getOnlyCommercePlatformIfAvailable()
    {
        $list = parent::getPaymentMethods();

        $transaction = $this->getFirstOpenPaymentTransaction();

        $paymentMethod = $transaction ? $transaction->getPaymentMethod() : null;

        if (
            isset($paymentMethod)
            && ($this->isPaypalCommercePlatform($paymentMethod))
        ) {
            // If customer return from Express checkout to confirm payment
            $list = array_filter($list, function ($method) {
                return $this->isPaypalCommercePlatform($method);
            });
        }

        return $list;
    }


    /**
     * Returns true if specified payment method is ExpressCheckout
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isExpressCheckout($method)
    {
        return $method->getServiceName() === 'ExpressCheckout';
    }

    /**
     * Returns true if specified payment method is ExpressCheckout
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isPaypalForMarketplaces($method)
    {
        return $method->getServiceName() === 'PaypalForMarketplaces';
    }

    /**
     * Returns true if specified payment method is ExpressCheckout
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isPaypalCredit($method)
    {
        return $method->getServiceName() === 'PaypalCredit';
    }

    /**
     * Returns true if specified payment method is ExpressCheckout
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isPaypalCommercePlatform($method)
    {
        return $method && $method->getServiceName() === 'PaypalCommercePlatform';
    }

    /**
     * Returns the associative array of transaction IDs: PPREF and/or PNREF
     *
     * @return array
     */
    public function getTransactionIds()
    {
        $result = [];

        foreach ($this->getPaymentTransactions() as $t) {
            if ($this->isPaypalMethod($t->getPaymentMethod())) {
                $isTestMode = $t->getDataCell('test_mode');

                if ($isTestMode !== null) {
                    $result[] = [
                        'url'   => '',
                        'name'  => 'Test mode',
                        'value' => 'yes',
                    ];
                }

                $ppref = $t->getDataCell('PPREF');
                if ($ppref !== null) {
                    $result[] = [
                        'url'   => $this->getTransactionIdURL($t, $ppref->getValue()),
                        'name'  => 'Unique PayPal transaction ID (PPREF)',
                        'value' => $ppref->getValue(),
                    ];
                }

                $pnref = $t->getDataCell('PNREF');
                if ($pnref !== null) {
                    $result[] = [
                        'url'   => '',
                        'name'  => 'Unique Payflow transaction ID (PNREF)',
                        'value' => $pnref->getValue(),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Place paypalCredit after paypalExpress
     *
     * @param array $paymentMethods Payment methods
     *
     * @return array
     */
    protected function sortPaypalMethods($paymentMethods)
    {
        $paypalCreditMethod = null;

        foreach ($paymentMethods as $key => $method) {
            if ($this->isPaypalCredit($method)) {
                $paypalCreditMethod = $method;
                unset($paymentMethods[$key]);
            }
        }


        if ($paypalCreditMethod) {
            $list = [];
            foreach ($paymentMethods as $method) {
                $list[] = $method;

                if ($this->isExpressCheckout($method)) {
                    $list[] = $paypalCreditMethod;
                }
            }

            $paymentMethods = $list;
        }

        return $paymentMethods;
    }



    /**
     * Get specific transaction URL on PayPal side
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction object
     * @param string                           $id          Transaction ID (PPREF)
     *
     * @return string
     */
    protected function getTransactionIdURL($transaction, $id)
    {
        $isTestMode = $transaction->getDataCell('test_mode');

        return $isTestMode !== null
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $id
            : 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $id;
    }

    /**
     * Return true if current payment method is PayPal
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isPaypalMethod($method)
    {
        return $method !== null
            && in_array(
                $method->getServiceName(),
                [
                    \CDev\Paypal\Main::PP_METHOD_PPA,
                    \CDev\Paypal\Main::PP_METHOD_PFL,
                    \CDev\Paypal\Main::PP_METHOD_EC,
                    \CDev\Paypal\Main::PP_METHOD_PPS,
                    \CDev\Paypal\Main::PP_METHOD_PC,
                    \CDev\Paypal\Main::PP_METHOD_PCP,
                ],
                true
            );
    }

    /**
     * Get paypal transaction id
     *
     * @return string
     */
    public function getPayPalTransactionId()
    {
        $transactionId = '';

        $lastTransaction = $this->getPaymentTransactions()->last();
        if ($lastTransaction && $this->isPaypalCommercePlatform($lastTransaction->getPaymentMethod())) {
            $transactionId = $lastTransaction->getDetail('PaypalCaptureID');
            if (!$transactionId) {
                $transactionId = $lastTransaction->getDetail('PaypalAuthorizationID');
            }
        }

        return $transactionId;
    }
}
