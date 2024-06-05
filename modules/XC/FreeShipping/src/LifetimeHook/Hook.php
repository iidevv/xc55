<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\FreeShipping\LifetimeHook;

use XC\FreeShipping\Core\MethodsLoader;
use XC\FreeShipping\Main;
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
        Main::switchFreeShippingMethods(false);
    }

    public function onRebuild(): void
    {
        MethodsLoader::process();
        Main::switchFreeShippingMethods(true);
    }

    public function onUpgradeTo5500(): void
    {
        $this->setModuleForShippingEntity();
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/FreeShipping/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    private function setModuleForShippingEntity(): void
    {
        $repo                = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
        $shippingMethodCodes = ['FREESHIP', 'FIXEDFEE'];

        foreach ($shippingMethodCodes as $code) {
            $shippingMethod = $repo->findOneBy([
                'code' => $code,
            ]);

            if ($shippingMethod) {
                /** @var \XLite\Model\Shipping\Method $shippingMethod */
                $shippingMethod->setModule('XC\FreeShipping');
            }
        }
    }
}
