<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\AvaTax\LifetimeHook;

use XCart\Doctrine\FixtureLoader;
use XLite\Core\Database;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onUpgradeTo5507(): void
    {
        $repo = Database::getRepo('XLite\Model\Config');

        $option = $repo->findOneBy([
            'name'     => 'collect_retail_delivery_fee',
            'category' => 'XC\AvaTax'
        ]);

        if (!$option) {
            $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/AvaTax/resources/hooks/upgrade/5.5/0.7/upgrade.yaml');
        }
    }
}
