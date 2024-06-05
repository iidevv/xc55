<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\LifetimeHook;

use XCart\Operation\Hook\SetServiceData;

final class Hook
{
    private SetServiceData $setServiceData;

    public function __construct(SetServiceData $setServiceData)
    {
        $this->setServiceData = $setServiceData;
    }

    public function onInstall(): void
    {
        ($this->setServiceData)();
    }
}
