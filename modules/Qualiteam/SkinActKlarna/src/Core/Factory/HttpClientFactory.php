<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Qualiteam\SkinActKlarna\Core\Configuration\Configuration;
use XLite\InjectLoggerTrait;

/**
 * Class http client factory
 */
class HttpClientFactory
{
    use InjectLoggerTrait;

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Configuration\Configuration $configuration
     *
     * @return Client
     */
    public static function createHttpClient(Configuration $configuration): Client
    {
        $handler = HandlerStack::create();
        $handler->push(
            Middleware::log(
                static::getStaticLogger(),
                new MessageFormatter()
            )
        );

        return new Client(['base_uri' => $configuration->getUrl(), 'handler' => $handler]);
    }
}
