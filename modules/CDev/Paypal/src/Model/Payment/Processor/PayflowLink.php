<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Payment\Processor;

/**
 * Payflow Link payment processor
 */
class PayflowLink extends \CDev\Paypal\Model\Payment\Processor\APaypal
{
    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/referral/paypal-payflow-link?partner_id=';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'https://support.x-cart.com/en/articles/5322860-paypal-payflow-link';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $method = \CDev\Paypal\Main::getPaymentMethod(
            \CDev\Paypal\Main::PP_METHOD_PFL
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
