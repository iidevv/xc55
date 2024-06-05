<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger;

use League\OAuth2\Client\Provider\AbstractProvider;

/**
 * @IgnoreAutowire
 */
final class OAuthConfig
{
    private AbstractProvider $provider;

    private array $options;

    private string $host;

    private int $port;

    /**
     * @return AbstractProvider
     */
    public function getProvider(): AbstractProvider
    {
        return $this->provider;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param AbstractProvider $provider
     */
    public function setProvider(AbstractProvider $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }
}
