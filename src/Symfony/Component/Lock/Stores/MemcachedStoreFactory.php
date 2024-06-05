<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Symfony\Component\Lock\Stores;

use Symfony\Component\Lock\Store\MemcachedStore;

class MemcachedStoreFactory
{
    private const DEFAULT_MEMCACHED_PORT = 11211;

    protected string $host;

    protected int $port = self::DEFAULT_MEMCACHED_PORT;

    public function __construct(
        string $host,
        int $port = self::DEFAULT_MEMCACHED_PORT
    ) {
        $this->port = $port;
        $this->host = $host;
    }

    public function getStore(): ?\Symfony\Component\Lock\Store\MemcachedStore
    {
        $memcached = new \Memcached();
        $memcached->addServer($this->host ?: 'localhost', $this->port ?: self::DEFAULT_MEMCACHED_PORT);
        return new MemcachedStore($memcached) ?: null;
    }
}
