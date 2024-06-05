<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Api;

/**
 * Interface api service
 */
interface ApiServiceInterface
{
    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return array|null
     * @throws \Qualiteam\SkinActKlarna\Core\Api\ApiException
     */
    public function sendRequest(string $method, string $uri = '', array $options = []): ?array;
}