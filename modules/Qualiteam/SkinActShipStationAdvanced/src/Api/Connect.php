<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Api;

use GuzzleHttp\Client;

class Connect
{
    protected Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function getGuzzleClient(): Client
    {
        return new Client([
            'base_uri' => $this->getShipStationUrl(),
            'headers'  => $this->getHeaders(),
        ]);
    }

    protected function getShipStationUrl(): string
    {
        return sprintf('https://%s', $this->getHost());
    }

    protected function getHost(): string
    {
        return 'ssapi.shipstation.com';
    }

    protected function getHeaders(): array
    {
        return [
            'Host'          => $this->getHost(),
            'Authorization' => $this->getAuth(),
            'Content-Type' => 'application/json',
        ];
    }

    protected function getAuth(): string
    {
        return $this->auth->getAuth();
    }
}