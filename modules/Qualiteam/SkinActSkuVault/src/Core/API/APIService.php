<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\API;

/**
 * Interface APIService
 */
interface APIService
{
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
    public function sendRequest(string $method, string $uri = '', array $options = []): array;
}