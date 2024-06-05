<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Shipping\Processor;

use XLite\InjectLoggerTrait;

/**
 * Shipping processor model
 * API documentation: https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/rating/default.jsf
 *
 */
class CanadaPost extends \XLite\Model\Shipping\Processor\AProcessor
{
    use InjectLoggerTrait;

    /**
     * $newMethods is used to prevent duplicating methods in database
     *
     * @var array
     */
    protected $newMethods = [];

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'capost';
    }

    /**
     * Returns settings template
     *
     * @return string
     */
    public function getSettingsTemplate()
    {
        return 'modules/XC/CanadaPost/settings/main.twig';
    }

    /**
     * Returns test template
     *
     * @return string
     */
    public function getTestTemplate()
    {
        return 'modules/XC/CanadaPost/settings/test.twig';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XC\CanadaPost\Main::getSettingsForm();
    }

    /**
     * Get shipping method admin zone icon URL
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Shipping\Method $method)
    {
        return true;
    }

    /**
     * Disable the possibility to edit the names of shipping methods in the interface of administrator
     *
     * @return boolean
     */
    public function isMethodNamesAdjustable()
    {
        return false;
    }

    /**
     * Get list of address fields required by shipping processor
     *
     * @return array
     */
    public function getRequiredAddressFields()
    {
        return [
            'country_code',
            'zipcode',
        ];
    }

    // {{{ Rates

    /**
     * Prepare input data from order modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $inputData Shipping order modifier
     *
     * @return array
     */
    protected function prepareDataFromModifier(\XLite\Logic\Order\Modifier\Shipping $inputData)
    {
        $data = [];
        $commonData = [];

        $sourceAddress = $inputData->getOrder()->getSourceAddress();
        if ($sourceAddress->getCountryCode() === 'CA') {
            $commonData['srcAddress'] = [
                'zipcode' => $sourceAddress->getZipcode(),
            ];
        }

        $commonData['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);

        if (!empty($commonData['srcAddress']) && !empty($commonData['dstAddress'])) {
            $data['packages'] = $this->getPackages($inputData);
            $data['commonData'] = $commonData;
        }

        return $data;
    }

    /**
     * Post process input data
     *
     * @param array $inputData Prepared input data
     *
     * @return array
     */
    protected function postProcessInputData(array $inputData)
    {
        $commonData = $inputData['commonData'] ?? [];
        unset($inputData['commonData']);

        $dstAddress = $commonData['dstAddress'] ?? [];
        if (!empty($dstAddress['country']) && $dstAddress['country'] === 'PR') {
            $dstAddress['country'] = 'US';
            $dstAddress['state'] = 'PR';
            $commonData['dstAddress'] = $dstAddress;
        }

        if (!empty($inputData['packages'])) {
            foreach ($inputData['packages'] as $key => $package) {
                $package = array_merge($package, $commonData);

                $package['weight'] = \XLite\Core\Converter::convertWeightUnits(
                    $package['weight'],
                    \XLite\Core\Config::getInstance()->Units->weight_unit,
                    'kg'
                );

                \XC\CanadaPost\Core\API::setCanadaPostConfig($this->getConfiguration());
                $package['subtotal'] = \XC\CanadaPost\Core\API::applyConversionRate($package['subtotal']);

                $inputData['packages'][$key] = $package;
            }
        } else {
            $inputData = [];
        }

        return parent::postProcessInputData($inputData);
    }

    /**
     * Performs request to carrier server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function performRequest($data, $ignoreCache)
    {
        $rates = [];
        $codeCounter = [];

        foreach ($data['packages'] as $pid => $package) {
            // Perform request for rates for each package
            $packageRates = $this->doQuery($package, $ignoreCache);

            if (!empty($packageRates)) {
                // Assemble package rates to the single rates array

                foreach ($packageRates as $code => $rate) {
                    if (!isset($rates[$code])) {
                        $rates[$code] = $rate;
                        $codeCounter[$code] = 1;
                    } else {
                        $rates[$code]->setBaseRate($rates[$code]->getBaseRate() + $rate->getBaseRate());
                        $codeCounter[$code] ++;
                    }
                }
            } else {
                $rates = [];
                break;
            }
        }

        if ($rates) {
            // Exclude rates for methods which are not available for all packages

            foreach ($codeCounter as $code => $cnt) {
                if (count($data['packages']) !== $cnt) {
                    unset($rates[$code]);
                }
            }
        }

        return $rates;
    }

    // }}}

    /**
     * Returns true if CanadaPost module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $config = $this->getConfiguration();

        return $config->user
            && $config->password
            && ($config->customer_number
                || $config->quote_type
                    === \XC\CanadaPost\Core\API::QUOTE_TYPE_NON_CONTRACTED
            );
    }

    /**
     * Get package limits
     *
     * @return array
     */
    protected function getPackageLimits()
    {
        $limits = parent::getPackageLimits();

        $config = $this->getConfiguration();

        // Weight in store weight units
        $limits['weight'] = \XLite\Core\Converter::convertWeightUnits(
            $config->max_weight,
            'kg',
            \XLite\Core\Config::getInstance()->Units->weight_unit
        );

        $limits['length'] = \XLite\Core\Converter::convertDimensionUnits(
            $config->length,
            'cm',
            \XLite\Core\Config::getInstance()->Units->dim_unit
        );

        $limits['width'] = \XLite\Core\Converter::convertDimensionUnits(
            $config->width,
            'cm',
            \XLite\Core\Config::getInstance()->Units->dim_unit
        );

        $limits['height'] = \XLite\Core\Converter::convertDimensionUnits(
            $config->height,
            'cm',
            \XLite\Core\Config::getInstance()->Units->dim_unit
        );

        return $limits;
    }

    /**
     * Low level query
     *
     * @param mixed   $data        Array of prepared package data
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return array
     */
    protected function doQuery($data, $ignoreCache)
    {
        $rates = [];

        $config = $this->getConfiguration();
        \XC\CanadaPost\Core\API::setCanadaPostConfig($config);

        $XMLData = $this->getXMLData($data);

        try {
            $postURL = \XC\CanadaPost\Core\API::getInstance()->getGetRatesEndpoint();

            if (!$ignoreCache) {
                $cachedRates = $this->getDataFromCache($XMLData);
            }

            if (isset($cachedRates)) {
                $result = $cachedRates;
            } elseif (\XLite\Model\Shipping::isIgnoreLongCalculations()) {
                // Ignore rates calculation
                return [];
            } else {
                $bouncer = new \XLite\Core\HTTP\Request($postURL);
                $bouncer->requestTimeout = 5;
                $bouncer->body = $XMLData;
                $bouncer->verb = 'POST';
                $bouncer->setHeader('Authorization', 'Basic ' . base64_encode($config->user . ':' . $config->password));
                $bouncer->setHeader('Accept', 'application/vnd.cpc.ship.rate-v2+xml');
                $bouncer->setHeader('Content-Type', 'application/vnd.cpc.ship.rate-v2+xml');
                $bouncer->setHeader('Accept-language', \XC\CanadaPost\Core\API::ACCEPT_LANGUAGE_EN);

                if (\XC\CanadaPost\Core\API::isOnBehalfOfAMerchant()) {
                    $bouncer->setHeader(
                        'Platform-id',
                        \XC\CanadaPost\Core\API::getInstance()->getPlatformId()
                    );
                }

                $response = $bouncer->sendRequest();

                $result = $response->body;

                if ($response->code == 200) {
                    $this->saveDataInCache($XMLData, $result);
                } else {
                    $this->setError(sprintf('Error while connecting to the Canada Post host (%s)', $postURL));
                }

                if ($config->debug_enabled) {
                    $this->getLogger('XC-CanadaPost')->debug('', [
                        'Request URL' => $postURL,
                        'Request XML (Get Rates)' => $XMLData,
                        'Response XML' => \XLite\Core\XML::getInstance()->getFormattedXML($result),
                    ]);
                }
            }

            // Save communication log for test request only (ignoreCache is set for test requests only)

            if ($ignoreCache === true) {
                $this->addApiCommunicationMessage([
                    'request_url'  => $postURL,
                    'request_data' => $XMLData,
                    'response'     => $result,
                ]);
            }

            $response = $this->parseResponse($result);

            if (!$this->hasError() && !isset($response['err_msg']) && !empty($response['services'])) {
                $conversionRate = \XC\CanadaPost\Core\API::getCurrencyConversionRate();

                foreach ($response['services'] as $service) {
                    $rate = new \XLite\Model\Shipping\Rate();

                    $method = $this->getMethodByCode($service['service_code'], self::STATE_ALL);

                    if ($method === null) {
                        // Unknown method received: add this to the database with disabled status
                        $this->createMethod($service['service_code'], $service['service_name'], false);
                    } elseif ($method->getEnabled()) {
                        // Method is registered and enabled

                        $rate->setMethod($method);
                        $rate->setBaseRate($service['rate'] * $conversionRate);

                        $rates[$service['service_code']] = $rate;
                    }
                }
            } elseif (!$this->hasError() || isset($response['err_msg'])) {
                $errorMessage = $response['err_msg'] ?? ($this->getError() ?: 'Unknown error');

                $this->setError($errorMessage);
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }

        return $rates;
    }

    /**
     * parses response and returns an associative array
     *
     * @param string $stringData XML response of capost api
     *
     * @return array
     */
    protected function parseResponse($stringData)
    {
        $result = [];

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($stringData, $err);

        if (isset($xmlParsed['messages'])) {
            $result['err_msg'] = $xml->getArrayByPath($xmlParsed, 'messages/message/description/0/#');
        }

        if (!isset($result['err_msg'])) {
            $services = $xml->getArrayByPath($xmlParsed, 'price-quotes/price-quote');

            if ($services) {
                foreach ($services as $k => $v) {
                    $result['services'][] = [
                        'service_code' => $xml->getArrayByPath($v, 'service-code/0/#'),
                        'service_name' => $xml->getArrayByPath($v, 'service-name/0/#'),
                        'rate' => $xml->getArrayByPath($v, 'price-details/0/#/due/0/#'),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Generate XML request
     *
     * @param array $data Array of package data
     *
     * @return string
     */
    protected function getXMLData($data)
    {
        $config = $this->getConfiguration();

        $xmlHeader = '<?xml version="1.0" encoding="utf-8"?' . '>';

        //  Option applies to this shipment.
        $opts = [];

        if (
            $config->coverage > 0
            && $data['subtotal'] > 0
        ) {
            // Add coverage (insuarance) option

            if ($config->coverage != 100) {
                $data['subtotal'] = $data['subtotal'] / 100 * $config->coverage;
            }

            $coverage = \XC\CanadaPost\Core\API::adjustFloatValue($data['subtotal'], 2, 0.01, 99999.99);

            $opts[] = <<<OUT
    <option>
        <option-code>COV</option-code>
        <option-amount>{$coverage}</option-amount>
    </option>
OUT;
        }

        $optionsXML = '';

        if ($opts) {
            $options = implode(PHP_EOL, $opts);
            $optionsXML = <<<OUT
<options>
$options
</options>
OUT;
        }

        $contractId = '';
        $customerNumber = '';
        if ($config->quote_type === \XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED) {
            $customerNumber = <<<OUT
<customer-number>{$config->customer_number}</customer-number>
OUT;
            if ($config->contract_id) {
                $contractId = <<<OUT
<contract-id>{$config->contract_id}</contract-id>
OUT;
            }
        }

        $parcelCharacteristics = '';

        $data['weight'] = \XC\CanadaPost\Core\API::adjustFloatValue($data['weight'], 3, 0.001, 99.999);

        $weight = <<<OUT
<weight>{$data['weight']}</weight>
OUT;

        $dimensions = '';

        if (!empty($data['box'])) {
            $length = \XLite\Core\Converter::convertDimensionUnits(
                $data['box']['length'],
                \XLite\Core\Config::getInstance()->Units->dim_unit,
                'cm'
            );
            $width  = \XLite\Core\Converter::convertDimensionUnits(
                $data['box']['width'],
                \XLite\Core\Config::getInstance()->Units->dim_unit,
                'cm'
            );
            $height = \XLite\Core\Converter::convertDimensionUnits(
                $data['box']['height'],
                \XLite\Core\Config::getInstance()->Units->dim_unit,
                'cm'
            );
        } elseif ($config->length && $config->width && $config->height) {
            $length = $config->length;
            $width  = $config->width;
            $height = $config->height;
        }

        if (!empty($length) && !empty($width) && !empty($height)) {
            $length = \XC\CanadaPost\Core\API::adjustFloatValue($length, 1, 0.1, 999.9);
            $width  = \XC\CanadaPost\Core\API::adjustFloatValue($width, 1, 0.1, 999.9);
            $height = \XC\CanadaPost\Core\API::adjustFloatValue($height, 1, 0.1, 999.9);

            $dimensions = <<<OUT
<dimensions>
    <length>{$length}</length>
    <width>{$width}</width>
    <height>{$height}</height>
</dimensions>
OUT;
        }
        $parcelCharacteristics .= <<<OUT
<parcel-characteristics>
    {$weight}
    {$dimensions}
</parcel-characteristics>
OUT;

        $dstPostalCode = \XC\CanadaPost\Core\API::strToUpper(
            preg_replace('/\s+/', '', $data['dstAddress']['zipcode'])
        );

        $srcPostalCode = \XC\CanadaPost\Core\API::strToUpper(
            preg_replace('/\s+/', '', $data['srcAddress']['zipcode'])
        );

        if ($data['dstAddress']['country'] === 'CA') {
            $destination = <<<OUT
<domestic>
    <postal-code>{$dstPostalCode}</postal-code>
</domestic>
OUT;
        } elseif ($data['dstAddress']['country'] === 'US') {
            $destination = <<<OUT
<united-states>
    <zip-code>{$dstPostalCode}</zip-code>
</united-states>
OUT;
        } else {
            $destination = <<<OUT
<international>
    <country-code>{$data['dstAddress']['country']}</country-code>
</international>
OUT;
        }

        $quoteType = ($config->quote_type === \XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED)
            ? 'commercial'
            : 'counter';

        $request = <<<OUT
{$xmlHeader}
<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v2">
    {$customerNumber}
    <quote-type>{$quoteType}</quote-type>
    {$optionsXML}
    {$contractId}
    {$parcelCharacteristics}
    <origin-postal-code>{$srcPostalCode}</origin-postal-code>
    <destination>{$destination}</destination>
</mailing-scenario>
OUT;

        return $request;
    }

    /**
     * Add api communication message
     *
     * @param string $message API communication log message
     *
     * @return void
     */
    protected function addApiCommunicationMessage($message)
    {
        if (!empty($message['request_data'])) {
            $message['request_data'] = htmlentities(
                $this->filterRequestData($message['request_data'])
            );
        }

        if (!empty($message['response'])) {
            $message['response'] = htmlentities(\XLite\Core\XML::getInstance()->getFormattedXML($message['response']));
        }

        parent::addApiCommunicationMessage($message);
    }

    /**
     * Filter request data for logging
     *
     * @param string $data Request data
     *
     * @return string
     */
    protected function filterRequestData($data)
    {
        return preg_replace(
            [
                '|<customer-number>.+</customer-number>|i',
            ],
            [
                '<customer-number>xxx</customer-number>',
            ],
            $data
        );
    }
}
