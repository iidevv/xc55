<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\Banner\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onEnable(): void
    {
        $toUpdate = [];

        $allBanners = \XLite\Core\Database::getRepo('QSL\Banner\Model\Banner')->getAllBanners() ?: [];
        foreach ($allBanners as $banner) {
            $viewLists = \XLite\Core\Database::getRepo('XLite\Model\ViewList')->findByEntityId($banner->getId()) ?: [];
            if ($viewLists) {
                foreach ($viewLists as $viewList) {
                    if ($viewList->getDeleted()) {
                        $viewList->setDeleted(false);
                        $toUpdate[] = $viewList;
                    }
                }
            }
        }

        if ($toUpdate) {
            \XLite\Core\Database::getRepo('XLite\Model\ViewList')->updateInBatch($toUpdate);
        }
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'QSL/Banner/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }
}
