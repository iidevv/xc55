<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Api;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;

abstract class AShipstationApi
{
    protected Client $guzzle;

    public function __construct(Connect $connect)
    {
        $this->guzzle = $connect->getGuzzleClient();
    }

    /**
     * @throws GuzzleException
     */
    protected function putRequest(string $uri, array $params)
    {
        return $this->guzzle->put($uri, [
            'body' => json_encode($params),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    protected function getRequest(string $uri, array $params)
    {
        return $this->guzzle->get($uri, [
            'query' => $params,
        ]);
    }
}