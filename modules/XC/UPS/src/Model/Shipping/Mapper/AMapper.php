<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\Model\Shipping\Mapper;

use XLite\InjectLoggerTrait;
use XC\UPS;

abstract class AMapper implements IMapper
{
    use InjectLoggerTrait;

    protected static $packageCODRules = [
        [
            'src' => ['US', 'PR'],
            'dst' => ['US', 'PR'],
        ],
        [
            'src' => ['CA'],
            'dst' => ['CA', 'US'],
        ],
    ];

    protected static $shipmentCODRules = [
        [
            'src' => ['EU'],
            'dst' => ['EU'],
        ],
    ];

    /**
     * @var mixed
     */
    protected $inputData;

    /**
     * @var mixed
     */
    protected $outputData;

    /**
     * @var UPS\Model\Shipping\Processor\UPS
     */
    protected $processor;

    /**
     * @var IMapper
     */
    protected $nextMapper;

    /**
     * @var mixed[]
     */
    protected $additionalData;

    /**
     * Is mapper able to map?
     *
     * @return boolean
     */
    abstract protected function isApplicable();

    /**
     * Perform actual mapping
     *
     * @return mixed
     */
    abstract protected function performMap();

    /**
     * Post-process mapped data
     *
     * @param mixed $mapped mapped data to post-process
     *
     * @return mixed
     */
    abstract protected function postProcessMapped($mapped);


    protected static function getOriginCode($code)
    {
        // EU members (Poland is also EU member, but has different location in $upsServices)
        $euMembers = [
            'AT', // Austria
            'BE', // Belgium
            'BU', // Bulgaria
            'CY', // Cyprus
            'CZ', // Czech Republic
            'DK', // Denmark
            'EE', // Estonia
            'FI', // Finland
            'FR', // France
            'DE', // Germany
            'GR', // Greece
            'HU', // Hungary
            'IE', // Ireland
            'IT', // Italy
            'LV', // Latvia
            'LT', // Lithuania
            'LU', // Luxembourg
            'MT', // Malta
            'MC', // Monaco
            'NL', // Netherlands
            'PT', // Portugal
            'RO', // Romania
            'SK', // Slovakia
            'SI', // Slovenia
            'ES', // Spain
            'SE', // Sweden
            'GB', // United Kingdom
        ];

        if (in_array($code, ['US','CA','PR','MX','PL'], true)) {
            // Origin is US, Canada, Puerto Rico or Mexico
            $originCode = $code;
        } elseif (in_array($code, $euMembers, true)) {
            // Origin is European Union
            $originCode = 'EU';
        } else {
            // Origin is other countries
            $originCode = 'OTHER_ORIGINS';
        }

        return $originCode;
    }

    /**
     * @param UPS\Model\Shipping\Processor\UPS $processor Shipping processor
     */
    public function __construct(UPS\Model\Shipping\Processor\UPS $processor)
    {
        $this->processor = $processor;
        $this->additionalData = [];
    }

    /**
     * Set input data
     *
     * @param mixed  $inputData Input data
     * @param string $key       Additional data key OPTIONAL
     *
     * @return void
     */
    public function setInputData($inputData, $key = 'default')
    {
        if ($key === 'default') {
            $this->inputData = $inputData;
        } else {
            $this->additionalData[$key] = $inputData;
        }
    }

    /**
     * Set next mapper if current will not succeed
     *
     * @param IMapper $nextMapper Next mapper
     */
    public function setNextMapper(IMapper $nextMapper)
    {
        $this->nextMapper = $nextMapper;
    }

    /**
     * @return mixed
     */
    public function getMapped()
    {
        $result = null;

        if ($this->isApplicable()) {
            $result = $this->postProcessMapped($this->performMap());
        } elseif ($this->nextMapper) {
            $this->nextMapper->setInputData($this->inputData);
            $result = $this->nextMapper->getMapped();
        } else {
            $this->getLogger('XC-UPS')->error('Internal error in mapper ' . get_class($this));
        }

        return $result;
    }

    /**
     * Get additional data by key
     *
     * @param string $key Additional data key
     *
     * @return mixed|null
     */
    protected function getAdditionalData($key)
    {
        return $this->additionalData[$key] ?? null;
    }

    /**
     * @param string $srcCountry
     * @param string $dstCountry
     *
     * @return boolean
     */
    protected function isPackageCODAllowed($srcCountry, $dstCountry)
    {
        $srcOrigin = static::getOriginCode($srcCountry);
        $dstOrigin = static::getOriginCode($dstCountry);

        $config = $this->getConfiguration();
        $result = $this->isCODAllowed(static::$packageCODRules, $srcOrigin, $dstOrigin);

        if ($result && $srcOrigin === 'CA' && $dstOrigin === 'US' && $config->packaging_type === '01') {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $srcCountry
     * @param string $dstCountry
     *
     * @return boolean
     */
    protected function isShipmentCODAllowed($srcCountry, $dstCountry)
    {
        $srcOrigin = static::getOriginCode($srcCountry);
        $dstOrigin = static::getOriginCode($dstCountry);

        $config = $this->getConfiguration();
        $result = $this->isCODAllowed(static::$shipmentCODRules, $srcOrigin, $dstOrigin);

        if ($result && $config->pickup_type !== '01') {
            $result = false;
        }

        return $result;
    }

    /**
     * @param string $srcCountry
     * @param string $dstCountry
     *
     * @return boolean
     */
    protected function isAnyCODAllowed($srcCountry, $dstCountry)
    {
        return $this->isCODAllowed(
            array_merge(static::$packageCODRules, static::$shipmentCODRules),
            static::getOriginCode($srcCountry),
            static::getOriginCode($dstCountry)
        );
    }

    /**
     * @param array  $rules
     * @param string $srcOrigin
     * @param string $dstOrigin
     *
     * @return boolean
     */
    protected function isCODAllowed($rules, $srcOrigin, $dstOrigin)
    {
        foreach ($rules as $rule) {
            if (
                in_array($srcOrigin, $rule['src'], true)
                && (empty($rule['dst']) || in_array($dstOrigin, $rule['dst'], true))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \XLite\Core\ConfigCell
     */
    protected function getConfiguration()
    {
        return $this->processor->getConfiguration();
    }
}
