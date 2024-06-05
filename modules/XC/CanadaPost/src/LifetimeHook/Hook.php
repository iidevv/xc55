<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onInit(): void
    {
        // Register CanadaPost shipping processor
        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'XC\CanadaPost\Model\Shipping\Processor\CanadaPost'
        );
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/CanadaPost/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }
}
