<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger;

use Hayageek\OAuth2\Client\Provider\Yahoo;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Google;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;
use XCart\Exception\AuthProviderException;

class OAuthProviderFactory
{
    public const AUTH_OAUTH2_GOOGLE    = 'google';
    public const AUTH_OAUTH2_YAHOO     = 'yahoo';
    public const AUTH_OAUTH2_MICROSOFT = 'microsoft';

    /**
     * @throws AuthProviderException
     */
    public static function create(
        string $providerName,
        string $clientId,
        string $clientSecret,
        string $redirectUri = ''
    ): OAuthConfig {
        $params = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
            'accessType' => 'offline'
        ];

        switch ($providerName) {
            case static::AUTH_OAUTH2_GOOGLE:
                return static::createGoogleProvider($params);
            case static::AUTH_OAUTH2_YAHOO:
                return static::createYahooProvider($params);
            case static::AUTH_OAUTH2_MICROSOFT:
                return static::createMicrosoftProvider($params);
        }

        throw new AuthProviderException($providerName);
    }

    private static function createConfig(AbstractProvider $provider, array $options, string $host, int $port): OAuthConfig
    {
        $oAuthConfig = new OAuthConfig();
        $oAuthConfig->setProvider($provider);
        $oAuthConfig->setOptions($options);
        $oAuthConfig->setHost($host);
        $oAuthConfig->setPort($port);

        return $oAuthConfig;
    }

    private static function createGoogleProvider(array $params): OAuthConfig
    {
        $options = [
            'scope' => [
                'https://mail.google.com/'
            ]
        ];

        return static::createConfig(new Google($params), $options, 'smtp.gmail.com', 465);
    }

    private static function createYahooProvider(array $params): OAuthConfig
    {
        return static::createConfig(new Yahoo($params), [], 'smtp.mail.yahoo.com', 465);
    }

    private static function createMicrosoftProvider(array $params): OAuthConfig
    {
        $options = [
            'scope' => [
                'wl.imap',
                'wl.offline_access'
            ]
        ];

        return static::createConfig(new Microsoft($params), $options, '', 465);
    }
}
