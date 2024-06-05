<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use XLite\InjectLoggerTrait;

/**
 * Class guzzle service
 */
class GuzzleService implements ApiServiceInterface
{
    use InjectLoggerTrait;

    /**
     * @var Client
     */
    protected Client $httpClient;

    /**
     * @var array
     */
    protected array $successHttpCodes = [
        200,
        201,
        202,
        204,
    ];

    /**
     * @param Client        $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient    = $httpClient;
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest(string $method, string $uri = '', array $options = []): ?array
    {
        try {
            $response           = $this->httpClient->request(
                $method,
                $uri,
                $options
            );

            $responseStatusCode = $response->getStatusCode();
            if (!in_array($responseStatusCode, $this->successHttpCodes)) {
                $codesString = implode(' or ', $this->successHttpCodes);
                $this->getLogger('klarna.logger')->error('API error. Expected HTTP code "' . $codesString . '", received "' . $responseStatusCode . '"');

                return [
                    'success' => false,
                    'message' => 'API error. Expected HTTP code "' . $codesString . '", received "' . $responseStatusCode . '"',
                ];
            }

            $result = json_decode($response->getBody(), true);
            $result['headers'] = $response->getHeaders();

            return $result;
        } catch (TransferException $e) {
            $this->getLogger('klarna.logger')->error($e);

            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            return [
                'success' => false,
                'message' => $responseBodyAsString,
            ];
        }
    }
}
