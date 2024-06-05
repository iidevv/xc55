<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Symfony\Component\Lock\Stores;

use Symfony\Component\Lock\Exception\InvalidArgumentException;
use Symfony\Component\Lock\Store\FlockStore;

class FlockStoreFactory
{
    private ?string $path;

    public function __construct(
        ?string $path
    ) {
        $this->path = $path;
    }

    /**
     * @throws \Exception
     */
    public function getStore(): ?\Symfony\Component\Lock\Store\FlockStore
    {
        if (!empty($this->path)) {
            $fallbackDirs = [$this->path];
        }
        $fallbackDirs = array_merge($fallbackDirs ?? [], [null, LC_DIR_TMP . 'transaction_locks']);
        $fallbackDirs = array_unique($fallbackDirs);

        foreach ($fallbackDirs as $tmpPath) {
            try {
                if (empty($tmpPath)) {
                    $store = new FlockStore();// https://symfony.com/doc/5.4/components/lock.html#available-stores
                } else {
                    $store = new FlockStore($tmpPath);
                }
            } catch (InvalidArgumentException $e) {
                // fallback to the next temp dir in cycle
                // sys_get_temp_dir()(null) is preferable as it will be cleaned by OS
                $lastErr = $e->getMessage();
            }
            if (!empty($store)) {
                break;
            }
        }
        if (!empty($lastErr) || empty($store)) {
            throw new \Exception($lastErr ?? 'FlockStore was failed for unknown reason');
        }

        return $store ?? null;
    }
}
