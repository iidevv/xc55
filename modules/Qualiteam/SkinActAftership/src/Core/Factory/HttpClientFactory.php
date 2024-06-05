<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use XLite\InjectLoggerTrait;

/**
 * Class http client factory
 */
class HttpClientFactory
{
    use InjectLoggerTrait;

    /**
     * @param string $url
     *
     * @return Client
     */
    public static function createHttpClient(string $url): Client
    {
        $handler = HandlerStack::create();
        $handler->push(
            Middleware::log(
                static::getStaticLogger(),
                new MessageFormatter()
            )
        );

        return new Client(['base_uri' => $url, 'handler' => $handler]);
    }
}
