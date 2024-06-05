<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL;

/**
 * ClientFactory
 */
class ClientFactory
{
    /**
     * @param string $endpoint
     *
     * @return \XLite\Core\GraphQL\Client\WithXidCookie
     */
    public static function createWithXidCookie($endpoint)
    {
        // TODO: use different auth mechanism
        $cookies = [
            \XLite\Core\Session::ARGUMENT_NAME => '',
        ];

        return new Client\WithXidCookie(
            new \GuzzleHttp\Client(
                [
                    'base_uri' => $endpoint,
                    'timeout'  => 45,
                ] + static::getGuzzleClientDefaults()
            ),
            new ResponseBuilder(),
            $cookies,
        );
    }

    protected static function getGuzzleClientDefaults()
    {
        $defaults = [
            'verify'  => (bool) \Includes\Utils\ConfigParser::getOptions(['service', 'verify_certificate']),
            'handler' => \GuzzleHttp\HandlerStack::create(
                new \GuzzleHttp\Handler\CurlHandler([
                    'handle_factory' => new \GuzzleHttp\Handler\CurlFactory(0),
                ])
            ),
        ];

        if (
            ($authUser = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_user']))
            && ($authPass = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_pass']))
        ) {
            $defaults['auth'] = [
                $authUser,
                $authPass,
            ];
        }

        return $defaults;
    }
}
