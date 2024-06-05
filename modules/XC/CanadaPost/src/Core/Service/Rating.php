<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Core\Service;

use XLite\InjectLoggerTrait;

/**
 * Implementation of the Canada Post's "Rating" service
 *
 * Service Summary:
 *
 *   Use the rating services to get shipping costs between two points at various speeds of service and
 *   with requested add-on features.
 *
 * More info at:
 *
 *   https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/rating/default.jsf
 *
 * Implemented calls:
 *
 *   Get Service
 *
 */
class Rating extends \XC\CanadaPost\Core\Service\AService
{
    use InjectLoggerTrait;

    // {{{ "Get Service" call

    /**
     * Call "Get Service" request by the service code and country code
     *
     * @param string $code        Delivery service code
     * @param string $countryCode Country code (OPTIONAL)
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetServiceByCode($code, $countryCode = '')
    {
        $endpoint = 'https://'
            . $this->getApiHost(static::getCanadaPostConfig()->developer_mode)
            . '/rs/ship/service/' . $code;

        if (!empty($countryCode)) {
            $endpoint .= '?country=' .  $countryCode;
        }

        return $this->callGetServiceByEndpoint($endpoint);
    }

    /**
     * Call "Get Service" request by the provided endpoint (URL)
     *
     * @param string $endpoint     Service endpoint (URL)
     * @param string $acceptHeader Accept header value (OPTIONAL)
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetServiceByEndpoint($endpoint, $acceptHeader = 'application/vnd.cpc.ship.rate-v2+xml')
    {
        return $this->callGetService($endpoint, $acceptHeader);
    }

    /**
     * Call "Get Service" request
     *
     * Reason to Call:
     * To find out details for a given postal service such as the dimension and weight limits and the available options.
     *
     * @param string $endpoint     Service endpoint (URL)
     * @param string $acceptHeader Accept header value
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetService($endpoint, $acceptHeader)
    {
        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', $acceptHeader);
            $request->setHeader('Accept-language', $this->getAcceptLanguage());

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (
                isset($response->body)
                && !empty($response->body)
            ) {
                // Parse XML response to object
                $result = $this->parseResponseGetService($response->body);
            } else {
                // Register request error
                $result->errors = [
                    $this->createErrorMessage(
                        'INTERNAL',
                        sprintf('Error while connecting to the Canada Post host (%s) during "Get Service" request', $endpoint)
                    )
                ];
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                $this->getLogger('XC-CanadaPost')->debug('', [
                    'Request URL' => $endpoint,
                    'Request XML (Get Service)' => '',
                    'Response XML' => \XLite\Core\XML::getInstance()->getFormattedXML($response->body),
                ]);
            }
        } catch (\Exception $e) {
            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, [$errorMessage]);
        }

        return $result;
    }

    /**
     * Parse response of the "Get Service" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetService($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XC\CanadaPost\Core\XML::getInstance();

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {
            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);
        } elseif (isset($xmlParsed['service'])) {
            // Collect returned data from "Get Service" call
            $result->service = $xml::convertParsedXmlDocument($xmlParsed['service']);
        }

        return $result;
    }

    // }}}
}
