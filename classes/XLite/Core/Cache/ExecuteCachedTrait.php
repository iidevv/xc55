<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;

/**
 * Trait ExecuteCachedTrait
 * @todo    : add long lifetime cache
 * @package XLite\Core\Cache
 */
trait ExecuteCachedTrait
{
    private $ExecuteCachedTraitCache = [];

    /**
     * Callback will be executed only once unless it return null.
     * This cache is object ware.
     * The code below
     * ```php
     * protected $dataRuntimeCache;
     * public function getData()
     * {
     *     if (null === $this->dataRuntimeCache) {
     *         $this->dataRuntimeCache = $this->defineData();
     *     }
     *
     *     return $this->dataRuntimeCache;
     * }
     * ```
     * can be replaced with
     * ```php
     * use ExecuteCachedTrait;
     * public function getData()
     * {
     *     return $this->executeCachedRuntime([$this, 'defineData'], 'data-key');
     * }
     * ```
     * If $cacheKeyParts is omitted then function name will be used, 'getData' in this example.
     *
     * @todo: add additional callback params; use them in cacheKeyParts.
     *
     * @param callable          $callback      Callback (the way to get initial value)
     * @param array|string|null $cacheKeyParts Cache cell name (it may be caller method name) OPTIONAL
     * @param boolean           $force         Force flag OPTIONAL
     *
     * @return mixed
     */
    protected function executeCachedRuntime(callable $callback, $cacheKeyParts = null, $force = false)
    {
        if ($cacheKeyParts === null) {
            $cacheKeyParts = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        }

        $cacheKey = $this->getRuntimeCacheKey($this->getRuntimeCacheKeyParts($cacheKeyParts));

        if (!array_key_exists($cacheKey, $this->ExecuteCachedTraitCache) || $force) {
            $this->ExecuteCachedTraitCache[$cacheKey] = $callback();
        }

        return $this->ExecuteCachedTraitCache[$cacheKey];
    }

    /**
     * Calculate key for cache storage
     *
     * @param mixed $cacheKeyParts
     *
     * @return string
     */
    protected function getRuntimeCacheKey($cacheKeyParts)
    {
        return is_scalar($cacheKeyParts) ? (string)$cacheKeyParts : md5(serialize($cacheKeyParts));
    }

    /**
     * Store object ware cache
     *
     * @param array|string $cacheKeyParts
     * @param mixed        $data
     */
    protected function setRuntimeCache($cacheKeyParts, $data)
    {
        $key = $this->getRuntimeCacheKey($this->getRuntimeCacheKeyParts($cacheKeyParts));
        $this->ExecuteCachedTraitCache[$key] = $data;
    }

    /**
     * Get object ware cache
     *
     * @param array|string $cacheKeyParts
     *
     * @return mixed|null
     */
    protected function getRuntimeCache($cacheKeyParts)
    {
        $key = $this->getRuntimeCacheKey($this->getRuntimeCacheKeyParts($cacheKeyParts));
        return  $this->ExecuteCachedTraitCache[$key] ?? null;
    }

    /**
     * @param mixed $cacheKeyParts
     *
     * @return array
     */
    protected function getRuntimeCacheKeyParts($cacheKeyParts)
    {
        return [$cacheKeyParts];
    }
}
