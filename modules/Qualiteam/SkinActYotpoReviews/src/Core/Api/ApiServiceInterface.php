<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Api;

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
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Api\ApiException
     */
    public function sendRequest(string $method, string $uri = '', array $options = []): ?array;
}