<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\GuzzleException;
use Qualiteam\SkinActAftership\Core\Configuration\Configuration;
use XLite\InjectLoggerTrait;

/**
 * Class guzzle service
 */
class GuzzleService implements ApiService
{
    use InjectLoggerTrait;

    /**
     * @var Client
     */
    protected Client $httpClient;

    /**
     * @var Configuration
     */
    protected Configuration $configuration;

    /**
     * @var array
     */
    protected array $successHttpCodes = [
        200,
        201,
        202,
    ];

    /**
     * @param Client        $httpClient
     * @param Configuration $configuration
     */
    public function __construct(Client $httpClient, Configuration $configuration)
    {
        $this->httpClient    = $httpClient;
        $this->configuration = $configuration;
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return array
     * @throws ApiException|GuzzleException
     */
    public function sendRequest(string $method, string $uri = '', array $options = []): array
    {
        try {
            $options            = array_merge($options, [
                'headers' => $this->getHeaders(),
            ]);
            $response           = $this->httpClient->request(
                $method,
                $uri,
                $options
            );
            $responseStatusCode = $response->getStatusCode();
            if (!in_array($responseStatusCode, $this->successHttpCodes)) {
                $codesString = implode(' or ', $this->successHttpCodes);
                $this->getLogger('aftershipApi')->error('API error. Expected HTTP code "' . $codesString . '", received "' . $responseStatusCode . '"');

                return [
                    'success' => false,
                    'message' => 'API error. Expected HTTP code "' . $codesString . '", received "' . $responseStatusCode . '"',
                ];

                //throw new ApiException('API error. Expected HTTP code "' . $codesString . '", received "' . $responseStatusCode . '"');
            }

            return json_decode($response->getBody(), true);
        } catch (TransferException $e) {
            $this->getLogger('aftershipApi')->error($e);

            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            return [
                'success' => false,
                'message' => $responseBodyAsString,
            ];
            //throw new ApiException($e->getMessage(), 0, $e, $e->getResponse()->getBody()->getContents(), $e->getResponse());
        }
    }

    /**
     * Get headers for request
     *
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'Content-Type'      => 'application/json',
            'aftership-api-key' => $this->configuration->getApiKey(),
        ];
    }

}
