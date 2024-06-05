<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\Model\Shipping\API;

use XLite\InjectLoggerTrait;
use XC\UPS\Model\Shipping\Mapper;
use XC\UPS\Model\Shipping\Processor;

class UPS
{
    use InjectLoggerTrait;

    /**
     * @var Processor\UPS
     */
    protected $processor;

    /**
     * @param Processor\UPS $processor
     */
    public function __construct($processor)
    {
        $this->processor = $processor;
    }

    /**
     * Returns API endpoint
     *
     * @return string
     */
    protected function getApiURL()
    {
        return $this->processor->isTestMode()
            ? 'https://wwwcie.ups.com/ups.app/xml'
            : 'https://onlinetools.ups.com:443/ups.app/xml';
    }

    /**
     * @param array $inputData
     *
     * @return mixed
     */
    public function getRates($inputData)
    {
        $url = $this->getApiURL() . '/Rate';

        $request = new Request\XMLRequest($url, $inputData);
        $request->setInputMapper(new Mapper\Rate\InputMapper($this->processor));
        $request->setOutputMapper(new Mapper\Rate\OutputMapper($this->processor));
        $request->sendRequest();

        $this->processor->addApiCommunicationMessage(
            [
                'method' => __METHOD__,
                'URL' => $url,
                'request' => $request->getRawRequest(),
                'response' => $request->getRawResponse(),
            ]
        );

        $this->getLogger('XC-UPS')->debug('', [
            'method' => __METHOD__,
            'URL' => $url,
            'request' => $this->filterRequestData($request->getRawRequest()),
            'response' => $request->getRawResponse(),
        ]);

        return $request->getResponse();
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
                '|<AccessLicenseNumber>.+</AccessLicenseNumber>|i',
                '|<UserId>.+</UserId>|i',
                '|<Password>.+</Password>|i',
            ],
            [
                '<AccessLicenseNumber>xxx</AccessLicenseNumber>',
                '<UserId>xxx</UserId>',
                '<Password>xxx</Password>',
            ],
            $data
        );
    }

    /**
     * @param array $inputData
     *
     * @return mixed
     */
    public function getRatesCOD($inputData)
    {
        $url = $this->getApiURL() . '/Rate';

        $request = new Request\XMLRequest($url, $inputData);
        $request->setInputMapper(new Mapper\RateCOD\InputMapper($this->processor));
        $request->setOutputMapper(new Mapper\RateCOD\OutputMapper($this->processor));
        $request->sendRequest();

        $this->processor->addApiCommunicationMessage(
            [
                'method' => __METHOD__,
                'URL' => $url,
                'request' => $request->getRawRequest(),
                'response' => $request->getRawResponse(),
            ]
        );

        $this->getLogger('XC-UPS')->debug('', [
            'method' => __METHOD__,
            'URL' => $url,
            'request' => \XLite\Core\XML::getInstance()->getFormattedXML($request->getRawRequest()),
            'response' => \XLite\Core\XML::getInstance()->getFormattedXML($request->getRawResponse()),
        ]);

        return $request->getResponse();
    }
}
