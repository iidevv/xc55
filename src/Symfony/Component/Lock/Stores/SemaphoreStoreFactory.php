<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Symfony\Component\Lock\Stores;

use Symfony\Component\Lock\Store\SemaphoreStore;

class SemaphoreStoreFactory
{
    public function getStore(): ?\Symfony\Component\Lock\Store\SemaphoreStore
    {
        return new SemaphoreStore() ?: null;
    }
}
