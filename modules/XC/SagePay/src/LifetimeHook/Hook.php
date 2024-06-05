<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\SagePay\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onUpgradeTo5504(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/SagePay/resources/hooks/upgrade/5.5/0.4/upgrade.yaml');
        if ($this->changeName()) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    private function changeName()
    {
        /** @var \XLite\Model\Payment\Method $method */
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Opayo form protocol']) ?: null;

        if (!$method) {
            return false;
        }

        if ($method->getName() === 'Opayo form protocol') {
            $method->setName('Opayo gateway - Form protocol');
        }
        \XLite\Core\Database::getEM()->persist($method);

        return true;
    }
}
