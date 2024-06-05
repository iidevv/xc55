<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Logic\reCAPTCHA;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Core\Config;
use XLite\Core\Marketplace;
use XLite\Core\HTTP\Request as HttpRequest;
use XLite\InjectLoggerTrait;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Result;

/**
 * This class handles the communication with Google reCAPTCHA API servers.
 *
 * @see https://developers.google.com/recaptcha/docs/verify
 */
class Validator extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * Cached results of Google reCAPTCHA validations.
     *
     * @var array
     */
    protected $verified = [];

    /**
     * Check if Google reCAPTCHA validation is required for the customer
     * registration form.
     *
     * @return bool
     */
    public function isRequiredForRegistrationForm()
    {
        return $this->isConfigured()
            && $this->isNotVerifiedUser()
            && Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_register;
    }

    /**
     * Check if Google reCAPTCHA validation is required for the customer
     * sign-in form.
     *
     * @return bool
     */
    public function isRequiredForLoginForm()
    {
        return $this->isConfigured()
            && $this->isNotVerifiedUser()
            && Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_login;
    }

    /**
     * Check if Google reCAPTCHA validation is required for the password
     * recovery form.
     *
     * @return bool
     */
    public function isRequiredForRecoveryForm()
    {
        return $this->isConfigured()
            && $this->isNotVerifiedUser()
            && Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_recover;
    }

    /**
     * Check if Google reCAPTCHA validation is required for the contact form.
     *
     * @return bool
     */
    public function isRequiredForContactForm()
    {
        return $this->isConfigured()
            && $this->isNotVerifiedUser()
            && Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_contact;
    }

    /**
     * Check if Google reCAPTCHA validation is required for the vendor sign-up
     * form.
     *
     * @return bool
     */
    public function isRequiredForVendorForm()
    {
        return $this->isConfigured()
            && $this->isNotVerifiedUser()
            && Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_vendor;
    }

    /**
     * Check if Google reCAPTCHA validation is required for the
     * Newsletter Subscription form.
     * (supported only in API v3)
     *
     * @return bool
     */
    public function isRequiredForNewsletterSubscriptions()
    {
        return $this->isAPIv3SDKRequired()
            && Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_newsletter;
    }

    public function isAPIv3SDKRequired()
    {
        return \QSL\reCAPTCHA\Main::isAPIv3()
            && $this->isConfigured()
            && $this->isNotVerifiedUser();
    }

    /**
     * Verifies a Google reCAPTCHA response and returns the verification result.
     *
     * It uses a cache to prevent the same reCAPTCHA response from being
     * verified more than once (all subsequent verification requests for the
     * same response value will fail).
     *
     * @param string $response Value from the response field
     *
     * @return \QSL\reCAPTCHA\Logic\reCAPTCHA\Result
     */
    public function verify($response)
    {
        if (!isset($this->verified[$response])) {
            $this->verified[$response] = $this->sendVerificationRequest($response);
        }

        return $this->verified[$response];
    }

    /**
     * Selected validation challenge
     *
     * @return string
     */
    public function getChallengeCode()
    {
        $verified = reset($this->verified) ?: null;

        return $verified ? $verified->getChallengeCodeResult() : null;
    }

    /**
     * Validates a Google reCAPTCHA response and returns the verification result.
     *
     * @param string $response Value from the response field
     *
     * @return \QSL\reCAPTCHA\Logic\reCAPTCHA\Result
     */
    protected function sendVerificationRequest($response)
    {
        $key = $this->getPrivateKey();
        $httpResponse = null;
        if (!$key) {
            $result = new Result(false, Result::ERROR_NO_SECRET);
        } elseif (!$response) {
            $result = new Result(false, Result::ERROR_NO_RESPONSE);
        } else {
            $request      = $this->prepareVerificationRequest(
                $key,
                $response,
                Request::getInstance()->getClientIp()
            );
            $httpResponse = $request->sendRequest();
            $result       = Result::createFromJSON($httpResponse->body);
        }

        $this->log([
            'response'     => $response,
            'httpResponse' => $httpResponse ? $httpResponse : null,
            'result'       => $result,
        ]);

        return $result;
    }

    /**
     * Logging
     *
     * @param mixed $message Message or data
     */
    protected function log($message)
    {
        if ($_ENV['APP_DEBUG']) {
            $this->getLogger('QSL-reCAPTCHA')->debug('', $message);
        }
    }

    /**
     * Prepares an HTTP request to Google reCAPTCHA servers that will verify the user.
     *
     * @param string $secret   Secret Google reCAPTCHA key
     * @param string $response Value of the "g-recaptcha-response" POST parameter
     * @param string $ip       IP address of the visitor
     *
     * @return \XLite\Core\HTTP\Request
     */
    protected function prepareVerificationRequest($secret, $response, $ip)
    {
        $request       = new HttpRequest($this->getVerifyUrl());
        $request->body = [
            'secret'   => $secret,
            'response' => $response,
            'remoteip' => $ip,
        ];

        if (function_exists('curl_version')) {
            $request->setAdditionalOption(\CURLOPT_SSLVERSION, 1);
            $curlVersion = curl_version();

            if (
                $curlVersion
                && $curlVersion['ssl_version']
                && strpos($curlVersion['ssl_version'], 'NSS') !== 0
            ) {
                $request->setAdditionalOption(\CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            }
        }

        $request->requestTimeout = $this->getTimeoutPeriod();

        return $request;
    }

    /**
     * Returns URL to send verification POST requests to.
     *
     * @return string
     */
    protected function getVerifyUrl()
    {
        return 'https://www.google.com/recaptcha/api/siteverify';
    }

    /**
     * Returns the secure key for Google reCAPTCHA.
     *
     * @return string
     */
    protected function getPrivateKey()
    {
        return Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_private ?: '';
    }

    /**
     * Returns the request timeout period.
     *s
     * @return int
     */
    protected function getTimeoutPeriod()
    {
        return Marketplace::TTL_LONG;
    }

    /**
     * Check if all keys are specified.
     *
     * @return bool
     */
    protected function isConfigured()
    {
        $config = Config::getInstance()->QSL->reCAPTCHA;

        return $config->google_recaptcha_private && $config->google_recaptcha_public;
    }

    /**
     * Check if the Google reCAPTCHA validation is required for the user.
     *
     * @return bool
     */
    protected function isNotVerifiedUser()
    {
        return !Auth::getInstance()->isLogged();
    }
}
