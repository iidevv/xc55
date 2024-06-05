<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\Model\Shipping\Processor;

use PEAR2\HTTP\Request\Response;
use XLite\Core\ConfigCell;
use XLite\InjectLoggerTrait;

/**
 * FedEx shipping processor model
 * API documentation: FedEx Web Services, Developer Guide 2012, Ver.13 (XCN-1035)
 */
class FEDEX extends \XLite\Model\Shipping\Processor\AProcessor
{
    use InjectLoggerTrait;

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'fedex';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \CDev\FedEx\Main::getSettingsForm();
    }

    /**
     * Check test mode
     *
     * @return boolean
     */
    public function isTestMode()
    {
        /** @var ConfigCell $config */
        $config = $this->getConfiguration();

        return (bool) $config->test_mode;
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
     * Get list of address fields required by shipping processor
     *
     * @return array
     */
    public function getRequiredAddressFields()
    {
        return [
            'country_code',
            'state_id',
            'zipcode',
        ];
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
     * Returns API URL
     *
     * @return string
     */
    public function getApiURL()
    {
        $protocol = 'https://';

        $host = $this->isTestMode()
            ? 'wsbeta.fedex.com:443/web-services'
            : 'ws.fedex.com:443/web-services';

        return $protocol . $host;
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
        $result = [];

        $sourceAddress = $inputData->getOrder()->getSourceAddress();
        $result['srcAddress'] = [
            'zipcode' => $sourceAddress->getZipcode(),
            'country' => $sourceAddress->getCountryCode(),
            'city'    => $sourceAddress->getCity()
        ];

        if (
            $sourceAddress->getState()
            && in_array($sourceAddress->getCountryCode(), ['US', 'CA'])
        ) {
            $result['srcAddress']['state'] = $sourceAddress->getState()->getCode();
        }

        $result['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);

        if (empty($result['dstAddress']['country'])) {
            $result['dstAddress'] = null;
        } elseif (isset($result['dstAddress']['state'])) {
            if (!in_array($result['dstAddress']['country'], ['US', 'CA'])) {
                $result['dstAddress']['state'] = '';
            }
        }

        $result['packages'] = $this->getPackages($inputData);

        // Detect if COD payment method has been selected by customer on checkout
        if ($inputData->getOrder()->getFirstOpenPaymentTransaction()) {
            $paymentMethod = $inputData->getOrder()->getPaymentMethod();

            if ($paymentMethod && $paymentMethod->getServiceName() === 'COD_FEDEX') {
                $result['cod_enabled'] = true;
            }
        }

        return $result;
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
        $result = [];

        if (
            !empty($inputData['packages'])
            && !empty($inputData['srcAddress'])
            && !empty($inputData['dstAddress'])
        ) {
            $result = $inputData;
            $result['packages'] = [];

            foreach ($inputData['packages'] as $package) {
                $package['price'] = sprintf('%.2f', $package['subtotal']); // decimal, min=0.00, totalDigits=10
                $package['weight'] = round(
                    \XLite\Core\Converter::convertWeightUnits(
                        $package['weight'],
                        \XLite\Core\Config::getInstance()->Units->weight_unit,
                        'lbs'
                    ),
                    4
                );

                $result['packages'][] = $package;
            }
        }

        return parent::postProcessInputData($result);
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
        $xmlData = $this->getXMLData($data);

        try {
            if (!$ignoreCache) {
                $cachedRate = $this->getDataFromCache($xmlData);
            }

            $postURL = $this->getApiURL();
            $result = null;

            if (isset($cachedRate)) {
                $result = $cachedRate;
                $timestamp = $this->getDataFromCache($xmlData . '.timestamp');
            } elseif (\XLite\Model\Shipping::isIgnoreLongCalculations()) {
                // Ignore rates calculation
                return [];
            } else {
                $bouncer = new \XLite\Core\HTTP\Request($postURL);
                $bouncer->body = $xmlData;
                $bouncer->verb = 'POST';
                $bouncer->requestTimeout = 5;

                /** @var Response $response */
                $response = $bouncer->sendRequest();

                if ($response->code === 200 || !empty($response->body)) {
                    $result = $response->body;
                    if ($response->code === 200) {
                        $this->saveDataInCache($xmlData, $result);
                        $this->saveDataInCache($xmlData . '.timestamp', \XLite\Core\Converter::time());
                    }

                    $this->getLogger('CDev-FedEx')->debug('', [
                        'request_url'  => $postURL,
                        'request_data' => $this->filterRequestData($xmlData),
                        'response'     => \XLite\Core\XML::getInstance()->getFormattedXML($result),
                    ]);
                } else {
                    $this->setError(sprintf('Error while connecting to the FedEx host (%s)', $postURL));
                }
            }

            $response = !$this->hasError()
                ? $this->parseResponse($result, $timestamp ?? \XLite\Core\Converter::time())
                : [];

            //save communication log for test request only (ignoreCache is set for test requests only)
            if ($ignoreCache === true) {
                $this->addApiCommunicationMessage([
                    'request_url'  => $postURL,
                    'request_data' => $xmlData,
                    'response'     => $result,
                ]);
            }

            if (!$this->hasError() && !isset($response['err_msg'])) {
                foreach ($response as $code => $_rate) {
                    $rate = new \XLite\Model\Shipping\Rate();

                    $method = $this->getMethodByCode($code, static::STATE_ALL);

                    if ($method && $method->getEnabled()) {
                        // Method is registered and enabled

                        $rate->setMethod($method);
                        $rate->setBaseRate($_rate['amount']);

                        if (isset($_rate['delivery_time']) && $_rate['delivery_time']) {
                            $rate->setDeliveryTime($_rate['delivery_time']);
                        }

                        if (!empty($data['cod_enabled'])) {
                            $extraData = new \XLite\Core\CommonCell();
                            $extraData->cod_supported = true;
                            $extraData->cod_rate = $rate->getBaseRate();
                            $rate->setExtraData($extraData);
                        }

                        $rates[] = $rate;
                    }
                }
            } elseif (!$this->hasError()) {
                $this->setError($response['err_msg'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            $this->setError('Exception: ' . $e->getMessage());
        }

        return $rates;
    }

    /**
     * Returns list of registered(and translated) fedex delivery time labels
     *
     * @return array
     */
    public function getRegisteredTransitTimeLabels()
    {
        return [
            'EIGHTEEN_DAYS'  => 18,
            'EIGHT_DAYS'     => 8,
            'ELEVEN_DAYS'    => 11,
            'FIFTEEN_DAYS'   => 15,
            'FIVE_DAYS'      => 5,
            'FOURTEEN_DAYS'  => 14,
            'FOUR_DAYS'      => 4,
            'NINETEEN_DAYS'  => 19,
            'NINE_DAYS'      => 9,
            'ONE_DAY'        => 1,
            'SEVENTEEN_DAYS' => 17,
            'SEVEN_DAYS'     => 7,
            'SIXTEEN_DAYS'   => 16,
            'SIX_DAYS'       => 6,
            'TEN_DAYS'       => 10,
            'THIRTEEN_DAYS'  => 13,
            'THREE_DAYS'     => 3,
            'TWELVE_DAYS'    => 12,
            'TWENTY_DAYS'    => 20,
            'TWO_DAYS'       => 2,
        ];
    }

    /**
     * Returns prepared delivery time
     *
     * @param \XLite\Model\Shipping\Rate $rate
     *
     * @return string|null
     */
    public function prepareDeliveryTime(\XLite\Model\Shipping\Rate $rate)
    {
        $days = $rate->getDeliveryTime();

        if ($days !== null) {
            return static::t('X days', ['days' => $days]);
        }

        return null;
    }

    // }}}

    // {{{ Configuration

    /**
     * Returns true if FedEx module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        /** @var ConfigCell $config */
        $config = $this->getConfiguration();

        return $config->meter_number
               && $config->key
               && $config->password
               && $config->account_number;
    }

    /**
     * Get currency conversion rate
     *
     * @return float
     */
    protected function getCurrencyConversionRate()
    {
        /** @var ConfigCell $config */
        $config = $this->getConfiguration();

        return ((float) $config->currency_rate) ?: 1;
    }

    // }}}

    // {{{ Package

    /**
     * Get package limits
     *
     * @return array
     */
    protected function getPackageLimits()
    {
        $limits = parent::getPackageLimits();
        /** @var ConfigCell $config */
        $config = $this->getConfiguration();

        // Weight in store weight units
        $limits['weight'] = \XLite\Core\Converter::convertWeightUnits(
            $config->max_weight,
            'lbs',
            \XLite\Core\Config::getInstance()->Units->weight_unit
        );

        [$limits['length'], $limits['width'], $limits['height']] = $config->dimensions;

        return $limits;
    }

    // }}}

    // {{{ Tracking information

    /**
     * This method must return the URL to the detailed tracking information about the package.
     * Tracking number is provided.
     *
     * @param string $trackingNumber Tracking number
     *
     * @return string
     */
    public function getTrackingInformationURL($trackingNumber)
    {
        return 'https://www.fedex.com/apps/fedextrack/index.html?' . $this->getTrackingURLParams($trackingNumber);
    }

    /**
     * Defines the form parameters of tracking information form
     *
     * @param string $trackingNumber Tracking number
     *
     * @return array Array of form parameters
     */
    public function getTrackingInformationParams($trackingNumber)
    {
        $list = parent::getTrackingInformationParams($trackingNumber);
        $list['tracknumbers'] = $trackingNumber;
        $list['ascend_header'] = 1;
        $list['clienttype'] = 'dotcom';
        $list['cntry_code'] = 'us';
        $list['language'] = 'english';

        return $list;
    }

    // }}}

    // {{{ Cache

    /**
     * Get key hash (to use this for caching rates)
     *
     * @param string $key Key value
     *
     * @return string
     */
    protected function getKeyHash($key)
    {
        $key = preg_replace('/<ShipTimestamp>.+<\/ShipTimestamp>/i', '', $key);

        return parent::getKeyHash($key);
    }

    // }}}

    // {{{ Logging

    /**
     * Add api communication message
     *
     * @param array $message API communication log message
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
                '|<AccountNumber>.+</AccountNumber>|i',
                '|<MeterNumber>.+</MeterNumber>|i',
                '|<Key>.+</Key>|i',
                '|<Password>.+</Password>|i',
            ],
            [
                '<AccountNumber>xxx</AccountNumber>',
                '<MeterNumber>xxx</MeterNumber>',
                '<Key>xxx</Key>',
                '<Password>xxx</Password>',
            ],
            $data
        );
    }

    // }}}

    // {{{ COD

    /**
     * Check if 'Cash on delivery (FedEx)' payment method enabled
     *
     * @return boolean
     */
    public static function isCODPaymentEnabled()
    {
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => 'COD_FEDEX']);

        return $method && $method->getEnabled();
    }

    /**
     * Check if COD is allowed
     *
     * @param array $data Input data array
     *
     * @return boolean
     */
    protected function isCODAllowed($data)
    {
        return true;
    }

    // }}}

    // {{{ Internals

    /**
     * Check if SmartPost service should be used
     *
     * @param array $fedexOptions FedEx options array
     *
     * @return boolean
     */
    protected function isSmartPost($fedexOptions)
    {
        return isset($fedexOptions['fxsp']) && $fedexOptions['fxsp'];
    }

    /**
     * Check if Ground service should be used
     *
     * @param array $fedexOptions FedEx options array
     *
     * @return boolean
     */
    protected function isGround($fedexOptions)
    {
        return isset($fedexOptions['fdxg']) && $fedexOptions['fdxg'];
    }

    /**
     * Check if Express service should be used
     *
     * @param array $fedexOptions FedEx options array
     *
     * @return boolean
     */
    protected function isExpress($fedexOptions)
    {
        return isset($fedexOptions['fdxe']) && $fedexOptions['fdxe'];
    }

    protected function prepareRequestedShipmentXML(array $data, array $fedexOptions): string
    {
        $result = [
            <<<OUT
                <ShipTimestamp>{$fedexOptions['ship_date_ready']}</ShipTimestamp>
OUT
        ];

        if ($this->isFedexOneRateEnabled($data, $fedexOptions)) {
            $result[] = <<<OUT
                <PackagingType>{$fedexOptions['packaging']}</PackagingType>
OUT;
        } else {
            $result[] = <<<OUT
                <DropoffType>{$fedexOptions['dropoff_type']}</DropoffType>
OUT;
        }

        $result[] = <<<OUT
                <PreferredCurrency>{$fedexOptions['currency_code']}</PreferredCurrency>
OUT;

        return implode("\n", $result);
    }

    /**
     * Returns XML-formatted request string for current type of API
     *
     * @param array $data Array of request values
     *
     * @return string
     */
    protected function getXMLData($data)
    {
        /** @var ConfigCell $config */
        $config = $this->getConfiguration();
        $fedexOptions = $config->getData();

        // Define ship date
        $fedexOptions['ship_date_ready']
            = date('c', \XLite\Core\Converter::time() + ((int)$fedexOptions['ship_date']) * 24 * 3600);

        // Define available carrier codes
        $carrierCodes = '';

        foreach (['fdxe', 'fdxg', 'fxsp'] as $code) {
            if (isset($fedexOptions[$code]) && $fedexOptions[$code]) {
                $carrierCodes
                    .= str_repeat(' ', 9) . '<CarrierCodes>' . strtoupper($code) . '</CarrierCodes>' . PHP_EOL;
            }
        }

        $rateRequestType = $fedexOptions['rate_request_type'] ?? 'NONE';

        // Define address fields
        $fedexOptions['destination_state_code']
            = ($data['dstAddress']['state'] ?? '');

        $fedexOptions['destination_country_code']
            = ($data['dstAddress']['country'] ?? '');

        $fedexOptions['destination_city']
            = ($data['dstAddress']['city'] ?? '');

        $fedexOptions['destination_postal_code']
            = ($data['dstAddress']['zipcode'] ?? '');

        $fedexOptions['origin_state_code']
            = ($data['srcAddress']['state'] ?? '');

        $fedexOptions['origin_country_code']
            = ($data['srcAddress']['country'] ?? '');

        $fedexOptions['origin_city']
            = ($data['srcAddress']['city'] ?? '');

        $fedexOptions['origin_postal_code']
            = ($data['srcAddress']['zipcode'] ?? '');

        // TODO: Move option to the settings page
        // Shipper address type: 1 - Residential, 0 - Commercial
        $fedexOptions['origin_address_type'] = ($fedexOptions['opt_residential_delivery'] ? 1 : 0);

        // TODO: Add this field to address book and get option from this
        //  address type: 1 - Residential, 0 - Commercial
        $fedexOptions['destination_address_type'] = (
            isset($data['dstAddress']['type'])
            && $data['dstAddress']['type'] === \XLite\View\FormField\Select\AddressType::TYPE_COMMERCIAL
        )
            ? 0
            : 1;

        $fedexOptions['dim_units'] = 'IN';
        $fedexOptions['weight_units'] = 'LB';

        $packagesCount = is_array($data['packages']) ? count($data['packages']) : 1;

        // Define packages XML
        $packagesXML = $this->preparePackagesXML($data, $fedexOptions);

        // Define Special services XML
        $specialServicesXML = $this->prepareSpecialServicesShipmentXML($data, $fedexOptions);

        $requestShipmentXML = $this->prepareRequestedShipmentXML($data, $fedexOptions);

        $smartPostDetail = '';
        if ($this->isSmartPost($fedexOptions)) {
            $hubId = $this->isTestMode()
                ? '5531'
                : $fedexOptions['fxsp_hub_id'];
            $indicia = $fedexOptions['fxsp_indicia'];
            $smartPostDetail = $this->prepareSmartPostDetails($indicia, $hubId);
        }

        $result = <<<OUT
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns="http://fedex.com/ws/rate/v31">
   <SOAP-ENV:Body>
      <RateRequest>
         <WebAuthenticationDetail>
            <UserCredential>
               <Key>{$fedexOptions['key']}</Key>
               <Password>{$fedexOptions['password']}</Password>
            </UserCredential>
         </WebAuthenticationDetail>
         <ClientDetail>
            <AccountNumber>{$fedexOptions['account_number']}</AccountNumber>
            <MeterNumber>{$fedexOptions['meter_number']}</MeterNumber>
         </ClientDetail>
         <TransactionDetail>
            <CustomerTransactionId>X-Cart 5: Rate an order packages v31</CustomerTransactionId>
         </TransactionDetail>
         <Version>
            <ServiceId>crs</ServiceId>
            <Major>31</Major>
            <Intermediate>0</Intermediate>
            <Minor>0</Minor>
         </Version>
         <ReturnTransitAndCommit>1</ReturnTransitAndCommit>
         {$carrierCodes}
         <RequestedShipment>
            {$requestShipmentXML}
            <Shipper>
               <AccountNumber>{$fedexOptions['account_number']}</AccountNumber>
               <Address>
                  <City>{$fedexOptions['origin_city']}</City>
                  <StateOrProvinceCode>{$fedexOptions['origin_state_code']}</StateOrProvinceCode>
                  <PostalCode>{$fedexOptions['origin_postal_code']}</PostalCode>
                  <CountryCode>{$fedexOptions['origin_country_code']}</CountryCode>
                  <Residential>{$fedexOptions['origin_address_type']}</Residential>
               </Address>
            </Shipper>
            <Recipient>
               <Address>
                  <City>{$fedexOptions['destination_city']}</City>
                  <StateOrProvinceCode>{$fedexOptions['destination_state_code']}</StateOrProvinceCode>
                  <PostalCode>{$fedexOptions['destination_postal_code']}</PostalCode>
                  <CountryCode>{$fedexOptions['destination_country_code']}</CountryCode>
                  <Residential>{$fedexOptions['destination_address_type']}</Residential>
               </Address>
            </Recipient>
            <ShippingChargesPayment>
               <PaymentType>SENDER</PaymentType>
               <Payor>
                  <ResponsibleParty>
                     <AccountNumber>{$fedexOptions['account_number']}</AccountNumber>
                  </ResponsibleParty>
               </Payor>
            </ShippingChargesPayment>
            {$specialServicesXML}
            {$smartPostDetail}
            <RateRequestTypes>{$rateRequestType}</RateRequestTypes>
            <PackageCount>{$packagesCount}</PackageCount>
            {$packagesXML}
         </RequestedShipment>
      </RateRequest>
   </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
OUT;

        return $result;
    }

    /**
     * Smart post details XML gtter
     * @return string
     */
    protected function prepareSmartPostDetails($indicia, $hubId)
    {
        return "
            <SmartPostDetail>
                <Indicia>$indicia</Indicia>
                <HubId>$hubId</HubId>
            </SmartPostDetail>";
    }

    /**
     * Return XML string with packages description
     *
     * @param array $data         Request data
     * @param array $fedexOptions FedEx options array
     *
     * @return string
     */
    protected function preparePackagesXML($data, $fedexOptions)
    {
        $i = 1;
        $itemsXML = '';

        $packages = $data['packages'];

        foreach ($packages as $pack) {
            if ($fedexOptions['packaging'] === 'YOUR_PACKAGING') {
                if (isset($pack['box'])) {
                    $length = $pack['box']['length'];
                    $width = $pack['box']['width'];
                    $height = $pack['box']['height'];
                } else {
                    [$length, $width, $height] = $fedexOptions['dimensions'];
                }

                $length = ceil($length);
                $width = ceil($width);
                $height = ceil($height);

                $dimensionsXML = <<<OUT
               <Dimensions>
                  <Length>{$length}</Length>
                  <Width>{$width}</Width>
                  <Height>{$height}</Height>
                  <Units>{$fedexOptions['dim_units']}</Units>
               </Dimensions>
OUT;
            } else {
                $dimensionsXML = '';
            }

            $weightXML = <<<OUT
               <Weight>
                  <Units>{$fedexOptions['weight_units']}</Units>
                  <Value>{$pack['weight']}</Value>
               </Weight>
OUT;

            // Declared value
            $declaredValueXML = '';

            $subtotal = $this->getPackagesSubtotal($data);

            if (
                !$this->isSmartPost($fedexOptions)
                && 0 < $subtotal
                && $fedexOptions['send_insured_value']
            ) {
                $declaredValueXML = <<<OUT
               <InsuredValue>
                 <Currency>{$fedexOptions['currency_code']}</Currency>
                 <Amount>{$subtotal}</Amount>
               </InsuredValue>
OUT;
            }

            $specialServicesXML = $this->prepareSpecialServicesPackageXML($data, $fedexOptions);

            $specialServicesXML = str_replace('{{fedex_weight}}', $pack['weight'], $specialServicesXML);

            $itemsXML .= <<<EOT
            <RequestedPackageLineItems>
               <SequenceNumber>{$i}</SequenceNumber>
               <GroupPackageCount>1</GroupPackageCount>
{$declaredValueXML}
{$weightXML}
{$dimensionsXML}
{$specialServicesXML}
            </RequestedPackageLineItems>

EOT;
            $i++;
        } // foreach ($packages as $pack)

        return $itemsXML;
    }

    /**
     * Return XML string with special services description
     *
     * @param array $data         Input data
     * @param array $fedexOptions FedEx options array
     *
     * @return string
     */
    protected function prepareSpecialServicesPackageXML($data, $fedexOptions)
    {
        $result = '';
        $specialServices = [];

        if (
            !empty($fedexOptions['dg_accessibility'])
            && !$this->isSmartPost($fedexOptions)
            && !$this->isGround($fedexOptions)
        ) {
            $specialServices[] = <<<OUT
                 <SpecialServiceTypes>DANGEROUS_GOODS</SpecialServiceTypes>
                 <DangerousGoodsDetail>
                   <Accessibility>{$fedexOptions['dg_accessibility']}</Accessibility>
                 </DangerousGoodsDetail>
OUT;
        }

        /* Option disabled
        if ('Y' == $fedexOptions['dry_ice']) {
            $specialServices[] = <<<OUT
                 <SpecialServiceTypes>DRY_ICE</SpecialServiceTypes>
                 <DryIceWeight>
                   <Units>LB</ns:Units>
                   <Value>{{fedex_weight}}</Value>
                 </DryIceWeight>
OUT;
        }
         */

        /* Option disabled
        if ('Y' == $fedexOptions['opt_nonstandard_container']) {
            $specialServices[] = <<<OUT
                 <SpecialServiceTypes>NON_STANDARD_CONTAINER</SpecialServiceTypes>
OUT;
        }
         */

        if (!empty($fedexOptions['signature'])) {
            $specialServices[] = <<<OUT
                 <SignatureOptionDetail>
                   <OptionType>{$fedexOptions['signature']}</OptionType>
                 </SignatureOptionDetail>
OUT;
        }

        if (!empty($specialServices)) {
            $specialServicesString = implode('', $specialServices);
            $result = <<<OUT
               <SpecialServicesRequested>
{$specialServicesString}
               </SpecialServicesRequested>
OUT;
        }

        return $result;
    }

    protected function isPackageTypeSupportedByFedexOneRate(string $packageType): bool
    {
        return in_array(
            $packageType,
            [
                'FEDEX_SMALL_BOX',
                'FEDEX_MEDIUM_BOX',
                'FEDEX_LARGE_BOX',
                'FEDEX_EXTRA_LARGE_BOX',
                'FEDEX_PAK',
                'FEDEX_TUBE',
                'FEDEX_ENVELOPE'
            ],
            true
        );
    }

    protected function arePackagesWeightsCorrectForOneRate(array $packages, string $packagingType): bool
    {
        foreach ($packages as $package) {
            if (
                ($packagingType === 'FEDEX_ENVELOPE' && $package['weight'] > 10)
                || $package['weight'] > 50
            ) {
                return false;
            }
        }

        return true;
    }

    protected function isFedexOneRateEnabled(array $data, array $fedexOptions): bool
    {
        return (
            !empty($fedexOptions['one_rate'])
            && ($data['srcAddress']['country'] ?? '') === 'US'
            && ($data['dstAddress']['country'] ?? '') === 'US'
            && (
                ($data['srcAddress']['state'] ?? '') !== 'HI'    // At the moment, One Rate doesn't support
                || ($data['dstAddress']['state'] ?? '') !== 'HI' // intra-Hawaii shipments.
            )
            && $this->isPackageTypeSupportedByFedexOneRate($fedexOptions['packaging'] ?? '')
            && $this->arePackagesWeightsCorrectForOneRate(
                (array)($data['packages'] ?? []),
                $fedexOptions['packaging'] ?? ''
            )
        );
    }

    /**
     * Return XML string with special services description
     *
     * @param array $data         Input data
     * @param array $fedexOptions FedEx options array
     *
     * @return string
     */
    protected function prepareSpecialServicesShipmentXML($data, $fedexOptions)
    {
        $result = '';
        $specialServices = [];

        if ($this->isFedexOneRateEnabled($data, $fedexOptions)) {
            $specialServices[] = <<<OUT
                <SpecialServiceTypes>FEDEX_ONE_RATE</SpecialServiceTypes>
OUT;
        }

        $specialServicesTypes = [];

        if (!empty($data['cod_enabled']) && $this->isCODAllowed($data)) {
            $subtotal = $this->getPackagesSubtotal($data);

            if (empty($fedexOptions['cod_type'])) {
                $fedexOptions['cod_type'] = 'ANY';
            }

            $specialServices[] = <<<OUT
                <SpecialServiceTypes>COD</SpecialServiceTypes>
                <CodDetail>
                  <CodCollectionAmount>
                    <Currency>{$fedexOptions['currency_code']}</Currency>
                    <Amount>{$subtotal}</Amount>
                  </CodCollectionAmount>
                  <CollectionType>{$fedexOptions['cod_type']}</CollectionType>
                </CodDetail>
OUT;
        }

        if (
            $fedexOptions['opt_saturday_pickup']
            && date('w', \XLite\Core\Converter::time() + ((int)$fedexOptions['ship_date']) * 24 * 3600) === '6'
        ) {
            $specialServicesTypes[] = 'SATURDAY_PICKUP';
        }

        foreach ($specialServicesTypes as $type) {
            $specialServices[] = <<<OUT
                <SpecialServiceTypes>{$type}</SpecialServiceTypes>
OUT;
        }

        if (!empty($specialServices)) {
            $specialServicesString = implode('', $specialServices);
            $result = <<<OUT
            <SpecialServicesRequested>
{$specialServicesString}
            </SpecialServicesRequested>
OUT;
        }

        return $result;
    }

    /**
     * Parses response and returns an associative array
     *
     * @param string  $stringData Response received from FedEx
     * @param integer $timestamp  Response timestamp
     *
     * @return array
     */
    protected function parseResponse($stringData, $timestamp)
    {
        $result = [];

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($stringData, $err);

        if (isset($xmlParsed['soapenv:Envelope']['#']['soapenv:Body'][0]['#']['soapenv:Fault'][0]['#'])) {
            // FedEx responses with error of request validation

            $result['err_msg'] = $xml->getArrayByPath(
                $xmlParsed,
                'soapenv:Envelope/#/soapenv:Body/0/#/soapenv:Fault/0/#/faultstring/0/#'
            );
        } else {
            $rateReply = $xml->getArrayByPath($xmlParsed, 'SOAP-ENV:Envelope/#/SOAP-ENV:Body/0/#/RateReply/0/#');

            $errorCodes = ['FAILURE', 'ERROR'];

            if (in_array($xml->getArrayByPath($rateReply, 'HighestSeverity/0/#'), $errorCodes, true)) {
                // FedEx failed to return valid rates

                $result['err_msg'] = $xml->getArrayByPath($rateReply, 'Notifications/0/#/Message/0/#');
                $result['err_code'] = $xml->getArrayByPath($rateReply, 'Notifications/0/#/Code/0/#');
            } else {
                // Success

                $rateDetails = $xml->getArrayByPath($rateReply, 'RateReplyDetails');

                if (!empty($rateDetails) && is_array($rateDetails)) {
                    /** @var ConfigCell $config */
                    $config = $this->getConfiguration();
                    $fedexOptions = $config->getData();

                    $conversionRate = $this->getCurrencyConversionRate();

                    $resultRates = [];

                    foreach ($rateDetails as $rate) {
                        $serviceType = $xml->getArrayByPath($rate, '#/ServiceType/0/#');

                        $ratedShipmentDetails = $xml->getArrayByPath($rate, '#/RatedShipmentDetails');

                        $transitTime = $xml->getArrayByPath($rate, '#/TransitTime/0/#');
                        $deliveryTimestamp = $xml->getArrayByPath($rate, '#/DeliveryTimestamp/0/#');

                        foreach ($ratedShipmentDetails as $rateDetails) {
                            $rateType = $xml->getArrayByPath(
                                $rateDetails,
                                '#/ShipmentRateDetail/RateType/0/#'
                            );

                            $resultRates[$serviceType][$rateType]['amount'] = $this->getRateAmount($rateDetails);

                            $variableHandlingCharge = $xml->getArrayByPath(
                                $rate,
                                '#/ShipmentRateDetail/TotalVariableHandlingCharges/VariableHandlingCharge/Amount/0/#'
                            );

                            $resultRates[$serviceType][$rateType]['amount'] += (float)$variableHandlingCharge;

                            if ($conversionRate !== 1) {
                                $resultRates[$serviceType][$rateType]['amount'] *= $conversionRate;
                            }

                            if ($deliveryTimestamp) {
                                $resultRates[$serviceType][$rateType]['delivery_time'] =
                                    round(
                                        (strtotime($deliveryTimestamp) - $timestamp)
                                        / \XLite\Core\Task\Base\Periodic::INT_1_DAY
                                    );
                            } elseif ($transitTime && isset($this->getRegisteredTransitTimeLabels()[$transitTime])) {
                                $transitTime = $this->getRegisteredTransitTimeLabels()[$transitTime];
                                $resultRates[$serviceType][$rateType]['delivery_time'] = $transitTime + $fedexOptions['ship_date'];
                            }
                        }
                    }

                    $prefferedType = 'PAYOR_' . ($fedexOptions['rate_request_type'] === 'LIST' ? 'LIST' : 'ACCOUNT') . '_PACKAGE';

                    foreach ($resultRates as $service => $serviceData) {
                        if (isset($serviceData[$prefferedType])) {
                            // Preffered request type is found - save this
                            $result[$service]['amount'] = $serviceData[$prefferedType]['amount'];

                            if (isset($serviceData[$prefferedType]['delivery_time'])) {
                                $result[$service]['delivery_time'] = $serviceData[$prefferedType]['delivery_time'];
                            }
                        } else {
                            // Preffered request type is found - search first available amount
                            foreach ($serviceData as $rateType => $rateData) {
                                $result[$service]['amount'] = $rateData['amount'];

                                if (isset($rateData['delivery_time'])) {
                                    $result[$service]['delivery_time'] = $rateData['delivery_time'];
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Log error
        if (isset($result['err_msg'])) {
            $this->getLogger('CDev-FedEx')->error('', [
                'Error'    => $result['err_msg'],
                'Response' => \XLite\Core\XML::getInstance()->getFormattedXML($stringData)
            ]);
        }

        return $result;
    }

    /**
     * Get shipping rate
     *
     * @param array $entry
     *
     * @return array
     */
    protected function getRateAmount($entry)
    {
        $xml = \XLite\Core\XML::getInstance();

        /** @var ConfigCell $config */
        $config = $this->getConfiguration();
        $currencyCode = $config->currency_code;

        $rateCurrency
            = $xml->getArrayByPath($entry, 'ShipmentRateDetail/TotalNetCharge/Currency/0/#');

        if ($rateCurrency !== $currencyCode) {
            // Currency conversion is needed
            $ratedShipmentDetails = $entry;

            // Try to find extact rate value
            $preciseRateFound = false;

            foreach ($ratedShipmentDetails as $key => $shipmentRateDetail) {
                $currencyExchangeRate =
                    $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/CurrencyExchangeRate/RATE/0/#');
                $fromCurrency = $xml->getArrayByPath(
                    $shipmentRateDetail,
                    'ShipmentRateDetail/CurrencyExchangeRate/FromCurrency/0/#'
                );
                $rateCurrency =
                    $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Currency/0/#');
                $estimatedRate =
                    $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Amount/0/#');

                if (
                    $currencyExchangeRate === '1.0'
                    && $fromCurrency === $currencyCode
                    && $rateCurrency === $currencyCode
                ) {
                    // This rate type can be used without conversion
                    $preciseRateFound = true;
                    break;
                }
            }

            if (!$preciseRateFound) {
                // Rate type without conversion is not found/ Use conversion
                foreach ($ratedShipmentDetails as $key => $shipmentRateDetail) {
                    $currencyExchangeRate =
                        $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/CurrencyExchangeRate/RATE/0/#');

                    if ($currencyExchangeRate === 0) {
                        continue;
                    }

                    $fromCurrency = $xml->getArrayByPath(
                        $shipmentRateDetail,
                        'ShipmentRateDetail/CurrencyExchangeRate/FromCurrency/0/#'
                    );

                    $intoCurrency = $xml->getArrayByPath(
                        $shipmentRateDetail,
                        'ShipmentRateDetail/CurrencyExchangeRate/IntoCurrency/0/#'
                    );

                    $rateCurrency =
                        $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Currency/0/#');

                    $estimatedRate =
                        $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Amount/0/#');

                    if ($fromCurrency === $rateCurrency) {
                        $estimatedRate *= $currencyExchangeRate;
                        break;
                    } elseif ($intoCurrency === $rateCurrency) {
                        $estimatedRate /= $currencyExchangeRate;
                        break;
                    }
                }
            }
        } // if ($rateCurrency != $currencyCode) {

        if (empty($estimatedRate)) {
            $estimatedRate
                = $xml->getArrayByPath($entry, 'ShipmentRateDetail/TotalNetCharge/Amount/0/#');
        }

        return $estimatedRate;
    }

    /**
     * Get sum of subtotals of all packages
     *
     * @param array $data Input data
     *
     * @return float
     */
    protected function getPackagesSubtotal($data)
    {
        $subtotal = 0;

        if (is_array($data)) {
            foreach ($data['packages'] as $pack) {
                $subtotal += (float)$pack['price'];
            }
        }

        return round($subtotal / $this->getCurrencyConversionRate(), 2);
    }

    // }}}
}
