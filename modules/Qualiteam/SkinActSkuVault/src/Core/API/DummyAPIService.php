<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\API;

use GuzzleHttp\Client;

/**
 * API Service
 */
class DummyAPIService implements APIService
{
    protected Client $httpClient;

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
        return [];
    }
}