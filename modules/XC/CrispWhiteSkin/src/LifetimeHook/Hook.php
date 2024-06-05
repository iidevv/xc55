<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\CrispWhiteSkin\LifetimeHook;

use XLite\Core\Layout;
use XLite\Logic\ImageResize\Generator;
use XC\CrispWhiteSkin\Main;
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
        Generator::addImageSizes(Main::getImageSizes());
    }

    public function onInstall(): void
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Layout',
            'name'     => 'layout_type_' . Layout::LAYOUT_GROUP_HOME,
            'value'    => Layout::LAYOUT_ONE_COLUMN,
        ]);
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/CrispWhiteSkin/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }
}
