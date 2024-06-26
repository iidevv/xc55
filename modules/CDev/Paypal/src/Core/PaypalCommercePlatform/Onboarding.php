<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\PaypalCommercePlatform;

use XLite\Core\HTTP\Request;
use XLite\InjectLoggerTrait;

class Onboarding
{
    use InjectLoggerTrait;

    /**
     * @var bool
     */
    protected $sandbox;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @param bool $sandbox
     */
    public function __construct($sandbox = false)
    {
        $this->sandbox = $sandbox;

        $this->accessToken = new AccessToken($sandbox);
    }

    /**
     * @param string $sellerNonce
     * @param string $returnUrl
     *
     * @return string
     */
    public function generatePaypalSignUpLink($sellerNonce, $returnUrl): string
    {
        $accessToken = $this->getSignUpLinkAccessToken();
        if (!$accessToken) {
            return '';
        }

        $request = new Request($this->getSignUpLinkUrl());

        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Authorization', 'Bearer ' . $accessToken);

        $request->verb = 'POST';

        $signUpLinkRequestData = $this->getSignUpLinkData($sellerNonce, $returnUrl);
        $request->body         = json_encode($signUpLinkRequestData);

        $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Generate SignUp link', $signUpLinkRequestData);

        $response = $request->sendRequest();

        if ($response && $response->code === 201) {
            $signUpLinkData = json_decode($response->body, true);

            $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: SignUp link', $signUpLinkData);

            foreach ($signUpLinkData['links'] ?? [] as $link) {
                if ($link['rel'] === 'action_url') {
                    return (string) $link['href'];
                }
            }
        } else {
            $this->getLogger('CDev-Paypal')->error('PaypalCommercePlatform Onboarding: Generate SignUp link error', [
                'response' => $response,
            ]);
        }

        return '';
    }

    /**
     * @param string $sellerNonce
     * @param string $authCode
     * @param string $sharedId
     *
     * @return string
     */
    public function getSellerAccessToken($sellerNonce, $authCode, $sharedId): string
    {
        $request = new Request($this->getSellerAccessTokenUrl());

        $request->setAdditionalOption(\CURLOPT_USERPWD, $sharedId . ":");
        $request->verb = 'POST';

        $sellerAccessTokenRequestData = [
            'grant_type'    => 'authorization_code',
            'code'          => $authCode,
            'code_verifier' => $sellerNonce,
        ];

        $request->body = $sellerAccessTokenRequestData;

        $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Get seller access token', $sellerAccessTokenRequestData);

        $response = $request->sendRequest();

        if ($response && $sellerAccessTokenData = @json_decode($response->body, true)) {
            $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Seller access token', $sellerAccessTokenData);

            return $sellerAccessTokenData['access_token'] ?? '';
        }

        $this->getLogger('CDev-Paypal')->error('PaypalCommercePlatform Onboarding: Get seller access token error', [
            'response' => $response
        ]);

        return '';
    }

    /**
     * @param string $accessToken
     *
     * @return array {
     *      client_id: string,
     *      client_secret: string,
     * }
     */
    public function getSellerCredentials($accessToken): array
    {
        $url     = $this->getSellerCredentialsUrl();
        $request = new Request($url);

        $request->setAdditionalOption(\CURLOPT_CONNECTTIMEOUT, 30);

        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Authorization', 'Bearer ' . $accessToken);

        $request->verb = 'GET';

        $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Get seller credentials', [
            'url' => $url,
        ]);

        $response = $request->sendRequest();

        if ($response && $sellerCredentials = @json_decode($response->body, true)) {
            $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Seller credentials', $sellerCredentials);

            return $sellerCredentials;
        }

        $this->getLogger('CDev-Paypal')->error('PaypalCommercePlatform Onboarding: Get seller credentials error', [
            'response' => $response,
        ]);

        return [];
    }

    /**
     * @param string $partnerId
     * @param string $merchantId
     * @param string $accessToken
     *
     * @return array
     */
    public function getMerchatOnboardingStatus($merchantId, $accessToken): array
    {
        $result = [
            'payments_receivable'     => false,
            'primary_email_confirmed' => false,
        ];

        $url     = $this->getMerchantOnboardingStatusUrl($merchantId);
        $request = new Request($url);

        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Authorization', 'Bearer ' . $accessToken);

        $request->verb = 'GET';

        $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Get merchant onboarding status', [
            'url' => $url,
        ]);

        $response = $request->sendRequest();

        if ($response && $merchantOnboardingStatusData = @json_decode($response->body, true)) {
            $this->getLogger('CDev-Paypal')->debug('PaypalCommercePlatform Onboarding: Merchant onboarding status', $merchantOnboardingStatusData);

            return $this->checkMerchantOnboardingStatusData($merchantOnboardingStatusData);
        }

        $this->getLogger('CDev-Paypal')->error('PaypalCommercePlatform Onboarding: Get merchant onboarding status error', [
            'response' => $response,
        ]);

        return $result;
    }

    /**
     * @param string $sellerNonce
     * @param string $returnUrl
     *
     * @return array
     */
    protected function getSignUpLinkData($sellerNonce, $returnUrl): array
    {
        return [
            'operations'              => [
                [
                    'operation'                  => 'API_INTEGRATION',
                    'api_integration_preference' => [
                        'rest_api_integration' => [
                            'integration_method'  => 'PAYPAL',
                            'integration_type'    => 'FIRST_PARTY',
                            'first_party_details' => [
                                'features'     => [
                                    'PAYMENT',
                                    'REFUND',
                                    //'PARTNER_FEE',
                                    //'DELAY_FUNDS_DISBURSEMENT',
                                    //'READ_SELLER_DISPUTE',
                                    //'UPDATE_SELLER_DISPUTE',
                                    'ACCESS_MERCHANT_INFORMATION',
                                ],
                                'seller_nonce' => $sellerNonce,
                            ],
                        ],
                    ],
                ],
            ],
            'products'                => ['PPCP'],
            'legal_consents'          => [
                [
                    'type'    => 'SHARE_DATA_CONSENT',
                    'granted' => true,
                ],
            ],
            'partner_config_override' => [
                'return_url' => $returnUrl,
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getSignUpLinkUrl(): string
    {
        return $this->isSandbox()
            ? 'https://api.sandbox.paypal.com/v2/customer/partner-referrals'
            : 'https://api.paypal.com/v2/customer/partner-referrals';
    }

    /**
     * @return string
     */
    protected function getSellerAccessTokenUrl(): string
    {
        return $this->isSandbox()
            ? 'https://api.sandbox.paypal.com/v1/oauth2/token'
            : 'https://api.paypal.com/v1/oauth2/token';
    }

    /**
     * @return string
     */
    protected function getSellerCredentialsUrl(): string
    {
        $accessTokenData = $this->accessToken->getAccessTokenData();

        return $this->isSandbox()
            ? "https://api.sandbox.paypal.com/v1/customer/partners/{$accessTokenData['partner_id']}/merchant-integrations/credentials"
            : "https://api.paypal.com/v1/customer/partners/{$accessTokenData['partner_id']}/merchant-integrations/credentials";
    }

    /**
     * @param string $merchantId
     *
     * @return string
     */
    protected function getMerchantOnboardingStatusUrl($merchantId): string
    {
        $accessTokenData = $this->accessToken->getAccessTokenData();

        return $this->isSandbox()
            ? "https://api.sandbox.paypal.com/v1/customer/partners/{$accessTokenData['partner_id']}/merchant-integrations/{$merchantId}"
            : "https://api.paypal.com/v1/customer/partners/{$accessTokenData['partner_id']}/merchant-integrations/{$merchantId}";
    }

    /**
     * @return string
     */
    protected function getSignUpLinkAccessToken(): string
    {
        $accessTokenData = $this->accessToken->getAccessTokenData();

        return $accessTokenData['access_token'] ?? '';
    }

    /**
     * @param array $merchantOnboardingStatusData
     *
     * @return array
     */
    protected function checkMerchantOnboardingStatusData($merchantOnboardingStatusData): array
    {
        return [
            'payments_receivable'     => $merchantOnboardingStatusData['payments_receivable'] === true,
            'primary_email_confirmed' => $merchantOnboardingStatusData['primary_email_confirmed'] === true,
        ];
    }

    /**
     * @return bool
     */
    protected function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
