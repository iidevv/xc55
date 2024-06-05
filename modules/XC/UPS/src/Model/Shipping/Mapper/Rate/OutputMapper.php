<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\Model\Shipping\Mapper\Rate;

use XLite\Core;
use XLite\Model\Shipping\Rate;
use XC\UPS;
use XC\UPS\Model\Shipping;

/**
 * Output mapper
 */
class OutputMapper extends Shipping\Mapper\AMapper
{
    /**
     * @var \SimpleXMLElement
     */
    protected $parsed;

    /**
     * @var float
     */
    protected $currencyRate;

    /**
     * This table provides correct service codes for different origins
     * <ServiceCode returned from UPS> => array (<origin> => <code of shipping method>)
     *
     * @var array
     */
    protected static $upsServices = [
        '01' => [
            'US' => 'NDA',
            'CA' => 'EXP',
            'PR' => 'NDA',
        ],
        '02' => [
            'US' => '2DA',
            'CA' => 'EXDSM',
            'PR' => '2DA',
        ],
        '03' => [
            'US' => 'GND',
            'PR' => 'GND',
        ],
        '07' => [
            'US' => 'WEXPSM',
            'EU' => 'EXP',
            'CA' => 'WEXP',
            'PL' => 'EXP',
            'PR' => 'WEXPSM',
            'MX' => 'EXP',
            'OTHER_ORIGINS' => 'EXP',
        ],
        '08' => [
            'US' => 'WEXDSM',
            'EU' => 'EXDSM',
            'PL' => 'EXDSM',
            'PR' => 'WEXDSM',
            'MX' => 'EXDSM',
            'OTHER_ORIGINS' => 'WEXDSM',
        ],
        '11' => [
            'US' => 'STD',
            'EU' => 'STD',
            'CA' => 'STD',
            'MX' => 'STD',
            'PL' => 'STD',
            'OTHER_ORIGINS' => 'STD',
        ],
        '12' => [
            'US' => '3DS',
            'CA' => '3DS',
        ],
        '13' => [
            'US' => 'NDAS',
            'CA' => 'EXPSAV',
        ],
        '14' => [
            'US' => 'NDAEAMSM',
            'CA' => 'EXPEAMSM',
            'PR' => 'NDAEAMSM',
        ],
        '54' => [
            'US' => 'WEXPPSM',
            'EU' => 'WEXPPSM',
            'PL' => 'EXPP',
            'PR' => 'WEXPPSM',
            'MX' => 'EXPP',
            'OTHER_ORIGINS' => 'WEXPPSM',
        ],
        '59' => [
            'US' => '2DAAM',
        ],
        '65' => [
            'US' => 'WSAV',
            'EU' => 'WSAV',
            'PL' => 'EXPSAV',
            'PR' => 'WSAV',
            'MX' => 'WSAV',
            'OTHER_ORIGINS' => 'WSAV',
        ],
        '82' => [
            'PL' => 'TSTD',
        ],
        '83' => [
            'PL' => 'TDC',
        ],
        '84' => [
            'PL' => 'TI',
        ],
        '85' => [
            'PL' => 'TEXP',
        ],
        '86' => [
            'PL' => 'TEXPS',
        ],
        '96' => [
            'US' => 'WEXPF',
            'EU' => 'WEXPF',
            'CA' => 'WEXPF',
            'PL' => 'WEXPF',
            'PR' => 'WEXPF',
            'MX' => 'WEXPF',
            'OTHER_ORIGINS' => 'WEXPF',
        ],
    ];

    /**
     * @param string $serviceCode
     * @param string $sourceOriginCode
     *
     * @return string|null
     */
    protected function getShippingServiceCode($serviceCode, $sourceOriginCode)
    {
        if (isset(static::$upsServices[$serviceCode][$sourceOriginCode])) {
            return static::$upsServices[$serviceCode][$sourceOriginCode];
        } elseif (static::$upsServices[$serviceCode]['OTHER_ORIGINS']) {
            return static::$upsServices[$serviceCode]['OTHER_ORIGINS'];
        }

        return null;
    }

    /**
     * @param UPS\Model\Shipping\Processor\UPS $processor Shipping processor
     */
    public function __construct(UPS\Model\Shipping\Processor\UPS $processor)
    {
        parent::__construct($processor);

        $this->currencyRate = (float) ($this->getConfiguration()->currency_rate ?: 1);

        libxml_use_internal_errors(true);
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        $parsed = $this->getParsed();
        if ($parsed->Response->Error) {
            return sprintf(
                'Error: %s - %s - %s',
                (string) $parsed->Response->Error->ErrorCode,
                (string) $parsed->Response->Error->ErrorSeverity,
                (string) $parsed->Response->Error->ErrorDescription
            );
        }

        return null;
    }

    /**
     * Is mapper able to map?
     *
     * @return boolean
     */
    protected function isApplicable()
    {
        return $this->inputData instanceof \PEAR2\HTTP\Request\Response
            && $this->getAdditionalData('request');
    }

    /**
     * Perform actual mapping
     *
     * @return Rate[]|null
     */
    protected function performMap()
    {
        $result = [];

        if ($this->isValid()) {
            foreach ($this->getParsed()->RatedShipment as $ratedShipment) {
                $rate = $this->getRate($ratedShipment);
                if ($rate) {
                    $result[] = $rate;
                }
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return Rate|null
     */
    protected function getRate($ratedShipment)
    {
        $result = null;

        $method = $this->getMethod($ratedShipment);
        if ($method) {
            $result = new Rate();
            $result->setBaseRate($this->getBaseRate($ratedShipment));
            $result->setMethod($method);

            $extraData = new Core\CommonCell();
            if ($ratedShipment->GuaranteedDaysToDelivery) {
                $extraData->deliveryDays = (string) $ratedShipment->GuaranteedDaysToDelivery;
            }

            if ($extraData->getData()) {
                $result->setExtraData($extraData);
            }
        }

        return $result;
    }

    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    protected function getMethod($ratedShipment)
    {
        $requestData = $this->getAdditionalData('request');
        $sourceOriginCode = static::getOriginCode($requestData['srcAddress']['country']);
        $code = static::getShippingServiceCode((string) $ratedShipment->Service->Code, $sourceOriginCode);

        return $code
            ? $this->processor->getMethodByCode($code)
            : null;
    }

    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return float
     */
    protected function getBaseRate($ratedShipment)
    {
        if ($ratedShipment->NegotiatedRates) {
            $result = (float) $ratedShipment->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
        } else {
            $result = (float) $ratedShipment->TotalCharges->MonetaryValue;
        }

        return $result * $this->currencyRate;
    }

    /**
     * @return \SimpleXMLElement
     */
    protected function getParsed()
    {
        if ($this->parsed === null) {
            $this->parsed = simplexml_load_string($this->inputData);
        }

        return $this->parsed;
    }

    /**
     * @return boolean
     */
    protected function isValid()
    {
        return $this->getError() === null;
    }

    /**
     * Post-process mapped data
     *
     * @param mixed $mapped mapped data to post-process
     *
     * @return mixed
     */
    protected function postProcessMapped($mapped)
    {
        return $mapped;
    }
}
