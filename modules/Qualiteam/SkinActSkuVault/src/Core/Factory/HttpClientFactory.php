<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Qualiteam\SkinActSkuVault\Core\Auth\AuthService;
use Qualiteam\SkinActSkuVault\Core\Configuration\Configuration;
use XLite\InjectLoggerTrait;

class HttpClientFactory
{
    use InjectLoggerTrait;

    /**
     * @param AuthService $authService
     * @param Configuration $configuration
     * @return Client
     */
    public static function createHttpClient(AuthService $authService, Configuration $configuration, string $baseUrl): Client
    {
        $handler = HandlerStack::create();
        $handler->push(
            Middleware::log(
                static::getStaticLogger(),
                new MessageFormatter()
            )
        );

        return new Client(['base_uri' => $baseUrl, 'handler' => $handler]);
    }
}
