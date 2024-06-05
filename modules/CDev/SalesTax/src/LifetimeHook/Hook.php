<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\SalesTax\LifetimeHook;

use XLite\Core\Database;
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
        $defaultZones = [
            'United States',
            'Europe'
        ];

        $tax = Database::getRepo('CDev\SalesTax\Model\Tax')->getTax();
        $zoneRepo = Database::getRepo('XLite\Model\Zone');
        $rateRepo = Database::getRepo('CDev\SalesTax\Model\Tax\Rate');

        foreach ($defaultZones as $zone_name) {
            $zone = $zoneRepo->findOneBy([
                'zone_name' => $zone_name
            ]);

            $rate = $rateRepo->findOneBy([
                'tax' => $tax,
                'zone' => $zone
            ]);

            if (!$rate) {
                $newRate = new \CDev\SalesTax\Model\Tax\Rate();
                $newRate->setTax($tax);
                $newRate->setZone($zone);
                Database::getEM()->persist($newRate);
                Database::getEM()->flush();
            }
        }
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'CDev/SalesTax/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }
}
