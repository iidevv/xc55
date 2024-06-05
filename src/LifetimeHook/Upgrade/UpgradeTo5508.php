<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\LifetimeHook\Upgrade;

use XCart\Doctrine\FixtureLoader;

final class UpgradeTo5508
{
    private FixtureLoader $fixtureLoader;

    public function __construct(
        FixtureLoader $fixtureLoader
    ) {
        $this->fixtureLoader  = $fixtureLoader;
    }

    public function onUpgrade(): void
    {
        $this->updateCarrierServicesPos();

        $this->fixtureLoader->loadYaml(LC_DIR_ROOT . 'upgrade/5.5/0.8/upgrade.yaml');
    }

    private function updateCarrierServicesPos(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Shipping\Method::class);

        $pos = 0;
        $processorsResult = $repo->createQueryBuilder('ma')
            ->select('ma.position')
            ->addSelect('ma.processor')
            ->addSelect('ma.method_id')
            ->andWhere('ma.carrier = :carrier')
            ->setParameter('carrier', '')
            ->addOrderBy('ma.position')
            ->addOrderBy('ma.method_id')
            ->getResult();

        foreach ($processorsResult as $row) {
            $carrierServices = $repo->createQueryBuilder('m')
                ->andWhere('m.processor = :processor AND (m.carrier != :carrier OR m.processor = :offline)')
                ->setParameter('processor', $row['processor'])
                ->setParameter('carrier', '')
                ->setParameter('offline', 'offline')
                ->addOrderBy('m.position')
                ->addOrderBy('m.method_id')
                ->getResult();

            /** @var \XLite\Model\Shipping\Method $service */
            foreach ($carrierServices as $service) {
                if (
                    $service->getProcessor() !== 'offline'
                    || (
                        $service->getProcessor() === 'offline'
                        && $service->getMethodId() === $row['method_id']
                    )
                ) {
                    $pos += 10;
                    $service->setPosition($pos);
                }
            }

            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Database::getEM()->clear();
        }
    }
}
