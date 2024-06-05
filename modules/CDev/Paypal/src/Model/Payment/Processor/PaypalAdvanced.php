<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Payment\Processor;

/**
 * Paypal Payments Advanced payment processor
 */
class PaypalAdvanced extends \CDev\Paypal\Model\Payment\Processor\APaypal
{
    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/referral/paypal-payments-advanced?partner_id=';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'https://support.x-cart.com/en/articles/5322787-paypal-payments-advanced';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $method = \CDev\Paypal\Main::getPaymentMethod(
            \CDev\Paypal\Main::PP_METHOD_PPA
        );

        $this->api->setMethod($method);
    }

    /**
     * Get the list of merchant countries where this payment processor can work
     *
     * @return array
     */
    public function getAllowedMerchantCountries()
    {
        return ['US', 'CA'];
    }

    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        $paypalCommercePlatform = \CDev\Paypal\Main::getPaymentMethod(
            \CDev\Paypal\Main::PP_METHOD_PCP
        );

        return parent::isConfigured($method)
            && !$paypalCommercePlatform->isEnabled();
    }

    /**
     * Get warning note by payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getWarningNote(\XLite\Model\Payment\Method $method)
    {
        if (parent::isConfigured($method)) {
            $paypalCommercePlatform = \CDev\Paypal\Main::getPaymentMethod(
                \CDev\Paypal\Main::PP_METHOD_PCP
            );

            if ($paypalCommercePlatform->isEnabled()) {
                return static::t('PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.');
            }
        }

        return parent::getWarningNote($method);
    }

    /**
     * @param \XLite\Model\Payment\Method $method
     *
     * @return string
     */
    public function getNotSwitchableReasonType(\XLite\Model\Payment\Method $method)
    {
        if (
            $method->getServiceName() === \CDev\Paypal\Main::PP_METHOD_PPA
            && !$this->isConfigured($method)
        ) {
            $paypalCommercePlatform = \CDev\Paypal\Main::getPaymentMethod(
                \CDev\Paypal\Main::PP_METHOD_PCP
            );

            if ($paypalCommercePlatform->isEnabled()) {
                return 'conflict';
            }
        }

        return '';
    }

    /**
     * Get allowed currencies
     * https://developer.paypal.com/webapps/developer/docs/classic/payflow/integration-guide/#paypal-currency-codes
     * https://developer.paypal.com/webapps/developer/docs/classic/paypal-payments-pro/integration-guide/WPWebsitePaymentsPro/#id25a6cc16-bbc4-4070-a575-9fad358f2b95__idd1ca306a-3829-4f55-930e-b295702a3e91
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array_merge(
            parent::getAllowedCurrencies($method),
            [
                'AUD', 'CAD', 'CZK', 'DKK', 'EUR',
                'HKD', 'HUF', 'JPY', 'NOK', 'NZD',
                'PLN', 'GBP', 'SGD', 'SEK', 'CHF',
                'USD',
            ]
        );
    }

    /**
     * Return array of parameters for 'CAPTURE' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     */
    protected function getCaptureRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $params = parent::getCaptureRequestParams($transaction);

        $params['CAPTURECOMPLETE'] = 'Y';

        return $params;
    }

    /**
     * Get iframe additional attributes
     *
     * @return array
     */
    protected function getIframeAdditionalAttributes()
    {
        return [
            'sandbox' => 'allow-top-navigation allow-scripts allow-forms allow-same-origin',
        ];
    }
}
