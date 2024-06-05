<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\EventListener;

use XCart\Domain\StaticConfigDomain;

class XCartBootListener
{
    private StaticConfigDomain $staticConfigDomain;

    public function __construct(
        StaticConfigDomain $staticConfigDomain
    ) {
        $this->staticConfigDomain = $staticConfigDomain;
    }

    public function onBoot(): void
    {
        $config = $this->staticConfigDomain->getConfig();

        $this->staticConfigDomain->setConfig($config);
    }
}
