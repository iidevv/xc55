<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\ProductVariants\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onInstall(): void
    {
        $queryBuilder = \XLite\Core\Database::getEM()->createQueryBuilder();
        $queryBuilder->update('XLite\Model\QuickData', 'qd')
            ->set('qd.minPrice', 'qd.price')
            ->set('qd.maxPrice', 'qd.price')
            ->getQuery()
            ->execute();
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/ProductVariants/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }
}
