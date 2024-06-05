<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Symfony\Component\Lock\Stores;

use Symfony\Component\Lock\Store\RedisStore;

class RedisStoreFactory
{
    private const DEFAULT_REDIS_PORT = 6379;

    protected string $host;

    protected int $port = self::DEFAULT_REDIS_PORT;

    public function __construct(
        string $host,
        int $port = self::DEFAULT_REDIS_PORT
    ) {
        $this->port = $port;
        $this->host = $host;
    }

    /**
     * @throws \RedisException
     */
    public function getStore(): ?\Symfony\Component\Lock\Store\RedisStore
    {
        // https://symfony.com/doc/5.4/components/lock.html#redisstore
        $redis = new \Redis();
        $redis->connect($this->host ?: 'localhost', $this->port ?: self::DEFAULT_REDIS_PORT);

        return new RedisStore($redis) ?: null;
    }
}
