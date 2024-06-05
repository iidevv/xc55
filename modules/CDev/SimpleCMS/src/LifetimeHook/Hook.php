<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\SimpleCMS\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onDisable(): void
    {
        \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')->deleteRootMenu();
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'CDev/SimpleCMS/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    public function onUpgradeTo5506(): void
    {
        $yamlFile = LC_DIR_MODULES . 'CDev/SimpleCMS/resources/hooks/upgrade/5.5/0.6/upgrade.yaml';

        if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
            \XLite\Core\Database::getEM()->flush();
        }
    }
}
