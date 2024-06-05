<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\ProductFeeds\LifetimeHook;

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
        $this->updateProductFeeds();
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'QSL/ProductFeeds/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    private function updateProductFeeds(): void
    {
        $repo = \XLite\Core\Database::getRepo(\QSL\ProductFeeds\Model\ProductFeed::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('pf');

            $qb
                ->update(\QSL\ProductFeeds\Model\ProductFeed::class, 'pf')
                ->set('pf.generatorClass', "REPLACE(pf.generatorClass, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('pf.generatorClass', "'XLite%'"))
                ->execute();
        }
    }
}
