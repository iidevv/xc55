<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core;

use XLite\InjectLoggerTrait;

/**
 * Abstract API
 */
class AAPI extends \XLite\Base\SuperClass
{
    use InjectLoggerTrait;

    /**
     * Payment method
     *
     * @var \XLite\Model\Payment\Method
     */
    protected $method = null;

    /**
     * Last response
     *
     * @var \PEAR2\HTTP\Request\Response
     */
    protected $response = null;


    // {{{ Common methods

    /**
     * Set payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Returns last response
     *
     * @return \PEAR2\HTTP\Request\Response
     */
    public function getLastResponse()
    {
        return $this->response;
    }

    /**
     * Returns Paypal API (Merchant API) setting value stored in Express Checkout payment method
     *
     * @param string $name Setting name
     *
     * @return string
     */
    protected function getSetting($name)
    {
        return $this->method
            ? $this->method->getSetting($name)
            : null;
    }

    /**
     * Return payment method processor
     *
     * @return \XLite\Model\Payment\Base\Processor
     */
    protected function getProcessor()
    {
        return $this->method
            ? $this->method->getProcessor()
            : null;
    }

    // }}}

    // {{{ Configuration

    /**
     * Return true if module is in test mode
     *
     * @return boolean
     */
    public function isTestMode()
    {
        return $this->getSetting('mode') == \XLite\View\FormField\Select\TestLiveMode::TEST;
    }

    // }}}

    // {{{ Helpers

    /**
     * Get shipping cost for set express checkout
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    protected function getShippingCost($order)
    {
        $result = null;

        $shippingModifier = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        if ($shippingModifier && $shippingModifier->canApply()) {
            /** @var \XLite\Model\Currency $currency */
            $currency = $order->getCurrency();

            $result = $currency->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING)
            );
        }

        return $result;
    }

    /**
     * Return true if transaction could be cancelled.
     * Transaction can be cancelled only before the cart become an order.
     *
     * @return boolean
     */
    public function isTransactionCancellable($transaction)
    {
        return $transaction
            && $transaction->getOrder() instanceof \XLite\Model\Cart;
    }

    // }}}

    // {{{ Backend request

    /**
     * Perform request
     *
     * @param string $type   Request type
     * @param array  $params Request params OPTIONAL
     *
     * @return array
     */
    public function doRequest($type, $params = [])
    {
        $result = [];

        $request = $this->createRequest($type, $params);

        $response = $request->sendRequest();
        $this->response = $response;

        if ($response instanceof \PEAR2\HTTP\Request\Response && $response->code == 200 && !empty($response->body)) {
            $result = $this->parseResponse($type, $response->body);
        }

        $this->getLogger('CDev-Paypal')->debug('doRequest', [
            'requestType'    => $type,
            'request'        => $request->body,
            'response'       => $response,
            'parsedResponse' => $result,
        ]);

        return $result;
    }

    /**
     * Returns new request object
     *
     * @param string $type   Request type
     * @param array  $params Request params
     *
     * @return \XLite\Core\HTTP\Request
     */
    protected function createRequest($type, $params)
    {
        $this->response = null;

        $request = new \XLite\Core\HTTP\Request($this->createUrl($type, $params));

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

        $request->body = $this->prepareParams($type, $params);
        $request->verb = 'POST';

        $request = $this->prepareRequest($request, $type, $params);

        return $request;
    }

    /**
     * Prepare request
     *
     * @param \XLite\Core\HTTP\Request $request Request
     * @param string                   $type    Request type
     * @param array                    $params  Request params
     *
     * @return \XLite\Core\HTTP\Request
     */
    protected function prepareRequest($request, $type, $params)
    {
        $method = sprintf('prepare%sRequest', \Includes\Utils\Converter::convertToUpperCamelCase($type));
        if (method_exists($this, $method)) {
            $request = $this->{$method}($request, $params);
        }

        return $request;
    }

    /**
     * Prepare request params
     *
     * @param string $type   Request type
     * @param array  $params Request params
     *
     * @return string
     */
    protected function prepareParams($type, $params)
    {
        $method = sprintf('prepare%sParams', \Includes\Utils\Converter::convertToUpperCamelCase($type));
        if (method_exists($this, $method)) {
            $params = $this->{$method}($params);
        }

        $params += $this->getCommonParams();

        return $this->convertParams($params);
    }

    /**
     * Convert request params from array to string
     * todo: use http_build_query with \PHP_QUERY_RFC3986 as fourth param at php 5.4+
     *
     * @param array $params Params
     *
     * @return string
     */
    protected function convertParams($params)
    {
        $parts = [];
        foreach ($params as $key => $value) {
            $parts[] = sprintf('%s=%s', $key, rawurlencode($value));
        }

        return implode('&', $parts);
    }

    /**
     * Returns common request params required for all requests
     *
     * @return array
     */
    protected function getCommonParams()
    {
        return [];
    }

    /**
     * Create url
     *
     * @param string $type   Request type
     * @param array  $params Request params
     *
     * @return string
     */
    protected function createUrl($type, $params)
    {
        $url = '';

        return $this->prepareUrl($url, $type, $params);
    }

    /**
     * Prepare url
     *
     * @param string $url    Url
     * @param string $type   Request type
     * @param array  $params Request params
     *
     * @return string
     */
    protected function prepareUrl($url, $type, $params)
    {
        $method = sprintf('prepare%sUrl', \Includes\Utils\Converter::convertToUpperCamelCase($type));
        if (method_exists($this, $method)) {
            $url = $this->{$method}($url, $params);
        }

        return $url;
    }

    /**
     * Returns parsed response
     *
     * @param string $type Response type
     * @param string $body Response body
     *
     * @return array
     */
    protected function parseResponse($type, $body)
    {
        $result = [];

        parse_str($body, $result);

        $method = sprintf('parse%sResponse', \Includes\Utils\Converter::convertToUpperCamelCase($type));
        if (method_exists($this, $method)) {
            $result = $this->{$method}($result);
        }

        return $result;
    }

    /**
     * Get amount for 'Capture' transaction
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return float
     */
    public function getCaptureAmount($transaction)
    {
        /** @var \XLite\Model\Order $order */
        $order = $transaction instanceof \XLite\Model\Payment\BackendTransaction
            ? $transaction->getPaymentTransaction()->getOrder()
            : $transaction->getOrder();

        /** @var \XLite\Model\Currency $currency */
        $currency = $order->getCurrency();

        return $currency->roundValue($order->getTotal());
    }

    /**
     * Get amount for 'Refund' transaction
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return float
     */
    public function getRefundAmount($transaction)
    {
        /** @var \XLite\Model\Order $order */
        $paymentTransaction = $transaction instanceof \XLite\Model\Payment\BackendTransaction
            ? $transaction->getPaymentTransaction()
            : $transaction;

        /** @var \XLite\Model\Currency $currency */
        $currency = $paymentTransaction->getCurrency() ?: $paymentTransaction->getOrder()->getCurrency();

        $amount = $transaction->getValue();

        return $currency->roundValue(max(0, $amount));
    }

    // }}}
}
