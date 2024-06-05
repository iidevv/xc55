<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Api;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Exception\GuzzleException;
use XLite\InjectLoggerTrait;

class ShipStationApi extends AShipstationApi
{
    use InjectLoggerTrait;

    /**
     * @throws GuzzleException
     */
    public function putProduct(int $productId, array $params)
    {
        return json_decode(
            $this->putRequest("/products/${productId}", $params)->getBody(),
            true
        );
    }

    /**
     * @throws GuzzleException
     */
    public function getProducts(array $params)
    {
        return json_decode(
            $this->getRequest('/products', $params)->getBody(),
            true
        );
    }
}
