<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\LifetimeHook;

use CDev\Paypal\Main;
use XCart\Doctrine\FixtureLoader;
use XLite\Core\Database;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onInit(): void
    {
        // overwrite possible old value in php.ini after upgrade from PHP 7.1.0-
        \ini_set('serialize_precision', -1);
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'CDev/Paypal/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    public function onUpgradeTo5501(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'CDev/Paypal/resources/hooks/upgrade/5.5/0.1/upgrade.yaml');
    }

    public function onUpgradeTo5509(): void
    {
        $paymentMethod = Database::getRepo('XLite\Model\Payment\Method')->findOneBy([
            'service_name' => Main::PP_METHOD_PCP
        ]);

        if ($paymentMethod) {
            $threeDSSoftExceptionSetting = Database::getRepo('XLite\Model\Payment\MethodSetting')->findOneBy([
                'payment_method' => $paymentMethod,
                'name'           => '3d_secure_soft_exception'
            ]);

            if ($threeDSSoftExceptionSetting) {
                $entityManager = Database::getEM();

                $entityManager->remove($threeDSSoftExceptionSetting);
                $entityManager->flush();
            }
        }
    }
}
