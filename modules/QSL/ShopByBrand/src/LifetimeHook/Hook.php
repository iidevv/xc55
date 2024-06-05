<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\ShopByBrand\LifetimeHook;

use CDev\SimpleCMS\Model\Menu;
use QSL\ShopByBrand\Main;
use XLite\Core\Database;
use XLite\Model\ImageSettings;
use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onRebuild(): void
    {
        if (class_exists(Menu::class)) {
            Main::addSimpleCMSMenuLink();
        }

        Database::getEM()->flush();
    }

    public function onUpgradeTo5500(): void
    {
        $this->updateImageSettings();
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'QSL/ShopByBrand/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    private function updateImageSettings(): void
    {
        $imageSettingsList = Database::getRepo(ImageSettings::class)->findByModuleName('QSL-ShopByBrand');

        if ($imageSettingsList) {
            foreach ($imageSettingsList as $imageSettings) {
                /** @var ImageSettings $imageSettings */
                $newModelValue = str_replace('XLite\\Module\\', '', $imageSettings->getModel());
                $imageSettings->setModel($newModelValue);
            }
        }
    }

    public function onUpgradeTo5506(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'QSL/ShopByBrand/resources/hooks/upgrade/5.5/0.6/upgrade.yaml');
    }
}
