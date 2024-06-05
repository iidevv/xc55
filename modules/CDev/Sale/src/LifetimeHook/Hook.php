<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\Sale\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'CDev/Sale/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    public function onUpgradeTo5502(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'CDev/Sale/resources/hooks/upgrade/5.5/0.2/upgrade.yaml');
    }
}
