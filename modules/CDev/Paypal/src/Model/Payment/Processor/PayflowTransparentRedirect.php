<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Payment\Processor;

/**
 * Payflow Transparent Redirect payment processor
 */
class PayflowTransparentRedirect extends \CDev\Paypal\Model\Payment\Processor\APaypal
{
    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/referral/paypal-payflow-link?partner_id=';

    protected $secureTokenId;

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/CDev/Paypal/transparent_redirect/input_template.twig';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $method = \CDev\Paypal\Main::getPaymentMethod(
            \CDev\Paypal\Main::PP_METHOD_PFTR
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

    // {{{ CreateSecureToken
    /**
     * Do CREATESECURETOKEN request and get SECURETOKEN from Paypal
     *
     * @return string
     */
    protected function doCreateSecureToken()
    {
        $token = parent::doCreateSecureToken();

        if ($token) {
            $method = \CDev\Paypal\Main::getPaymentMethod(
                \CDev\Paypal\Main::PP_METHOD_PFTR
            );

            \XLite\Core\Event::paypalTransparentRedirect(
                [
                    'token' => $token,
                    'secureTokenId' => $this->secureTokenId,
                    'action' => $this->isTestMode($method)
                        ? 'https://pilot-payflowlink.paypal.com/'
                        : 'https://payflowlink.paypal.com/',
                ]
            );
        }

        return $token;
    }

    /**
     * Get array of parameters for CREATESECURETOKEN request
     *
     * @return array
     */
    protected function getCreateSecureTokenRequestParams()
    {
        $params = parent::getCreateSecureTokenRequestParams();
        $params['SILENTTRAN'] = 'TRUE';

        $this->secureTokenId = $this->api->getSecureTokenId();

        return $params;
    }

    // }}}
}
