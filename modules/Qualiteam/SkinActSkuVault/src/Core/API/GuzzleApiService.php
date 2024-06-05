<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\API;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Client;

class GuzzleApiService implements APIService
{
    protected $httpClient;
    protected $successHttpCodes = [
        200,
        202,
    ];

    /**
     * Constructor
     *
     * @param Client $httpClient
     *
     * @return void
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return array
     * @throws APIException
     */
    public function sendRequest(string $method, string $uri = '', array $options = []): array
    {
        try {
            $options = array_merge($options, [
                'headers' => $this->getHeaders()
            ]);
            $response = $this->httpClient->request(
                $method,
                $uri,
                $options
            );
            $responseStatusCode = $response->getStatusCode();
            if (!in_array($responseStatusCode, $this->successHttpCodes)) {
                $codesString = implode(' or ', $this->successHttpCodes);
                throw new APIException('API error. Expected HTTP code "' . $codesString . '", received "' . $responseStatusCode . '"');
            }
            return json_decode($response->getBody(), true);
        } catch (TransferException $e) {
            throw new APIException($e->getMessage(), 0, $e, $e->getResponse()->getBody()->getContents(), $e->getResponse());
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
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }
}
