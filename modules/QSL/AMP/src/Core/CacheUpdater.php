<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

use XLite\Core\HTTP\Request;

/**
 * Methods to update AMP cache
 */
class CacheUpdater
{
    public const ENDPOINT = 'https://cdn.ampproject.org/update-ping/c/';

    /**
     * Update Google AMP Cache
     *
     * @param $url
     *
     * @return bool
     */
    public function updateUrl($url)
    {
        $https = preg_match('/^https:/', $url);

        $url = substr($url, strlen($https ? 'https://' : 'http://'));

        $apiUrl = self::ENDPOINT . ($https ? 's/' : '') . $url;

        $request = new Request($apiUrl);

        $response = $request->sendRequest();

        return $response->code === 204;
    }
}
