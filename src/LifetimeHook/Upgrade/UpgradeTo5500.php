<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\LifetimeHook\Upgrade;

use XCart\Doctrine\FixtureLoader;
use XCart\Operation\Hook\SetServiceData;

final class UpgradeTo5500
{
    private SetServiceData $setServiceData;

    private FixtureLoader $fixtureLoader;

    public function __construct(
        FixtureLoader $fixtureLoader,
        SetServiceData $setServiceData
    ) {
        $this->fixtureLoader  = $fixtureLoader;
        $this->setServiceData = $setServiceData;
    }

    public function onUpgrade(): void
    {
        $this->updatePaymentMethodProcessorClassName();
        $this->updateNotificationAvailable();
        $this->updateOrderModifiers();
        $this->updateConfigTypeClassName();
        $this->removeConfigOptions();
        $this->updateOrderSurcharges();
        $this->updateMoneyModificators();
        $this->updateTasks();

        ($this->setServiceData)();

        $this->fixtureLoader->loadYaml(LC_DIR_ROOT . 'upgrade/5.5/0.0/upgrade.yaml');
    }

    private function updatePaymentMethodProcessorClassName(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Payment\Method::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('p');

            $qb
                ->update(\XLite\Model\Payment\Method::class, 'p')
                ->set('p.class', $qb->expr()->substring('p.class', "'8'"))
                ->where($qb->expr()->like('p.class', "'Module%'"))
                ->execute();

            $qb = $repo->createPureQueryBuilder('p');

            $qb
                ->update(\XLite\Model\Payment\Method::class, 'p')
                ->set('p.class', $qb->expr()->concat("'XLite\\'", 'p.class'))
                ->where($qb->expr()->like('p.class', "'Model%'"))
                ->execute();
        }
    }

    private function updateNotificationAvailable(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Notification::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('n');

            $qb
                ->update(\XLite\Model\Notification::class, 'n')
                ->set('n.available', true)
                ->execute();
        }
    }

    private function updateOrderModifiers(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Order\Modifier::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('om');

            $qb
                ->update(\XLite\Model\Order\Modifier::class, 'om')
                ->set('om.class', $qb->expr()->substring('om.class', "'2'"))
                ->where($qb->expr()->like('om.class', "'\\\%'"))
                ->execute();

            $qb = $repo->createPureQueryBuilder('om');

            $qb
                ->update(\XLite\Model\Order\Modifier::class, 'om')
                ->set('om.class', "REPLACE(om.class, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('om.class', "'XLite\\\Module%'"))
                ->execute();
        }
    }

    private function updateOrderSurcharges(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Order\Surcharge::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('os');

            $qb
                ->update(\XLite\Model\Order\Surcharge::class, 'os')
                ->set('os.class', "REPLACE(os.class, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('os.class', "'\\\XLite\\\Module%'"))
                ->execute();
        }
    }

    private function updateMoneyModificators(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\MoneyModificator::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('mm');

            $qb
                ->update(\XLite\Model\MoneyModificator::class, 'mm')
                ->set('mm.class', "REPLACE(mm.class, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('mm.class', "'XLite\\\Module%'"))
                ->execute();
        }
    }

    private function updateConfigTypeClassName(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Config::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('c');

            $qb
                ->update(\XLite\Model\Config::class, 'c')
                ->set('c.type', "REPLACE(c.type, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('c.type', "'%XLite\\\Module%'"))
                ->execute();
        }
    }

    private function removeConfigOptions(): void
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');

        $optionsToRemove = [
            'clean_url_flag'       => 'CleanURL',
            'object_name'          => 'CleanURL',
            'clean_urls_about'     => 'CleanURL',
            'about_widget'         => 'CleanURL',
            'livechat_ad'          => 'Company',
            'cloud_domain'         => 'Company',
            'enable_vendor_filter' => 'XC\MultiVendor',
            'wizard_state'         => 'XC\Onboarding',
            'enable_tags_filter'   => 'XC\ProductFilter',
            'key'                  => 'XC\RESTAPI',
            'key_read'             => 'XC\RESTAPI',
            'currentPlan'          => 'XC\Cloud',
            'is_user_id_correct'   => 'XC\Concierge',
        ];

        foreach ($optionsToRemove as $optionName => $category) {
            $element = $repo->findOneBy([
                'name'     => $optionName,
                'category' => $category,
            ]);

            if ($element) {
                /** @var \XLite\Model\Config $element */
                $repo->delete($element, false);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    private function updateTasks(): void
    {
        $repo = \XLite\Core\Database::getRepo(\XLite\Model\Task::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('t');

            $qb
                ->update(\XLite\Model\Task::class, 't')
                ->set('t.owner', "REPLACE(t.owner, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('t.owner', "'XLite\\\Module%'"))
                ->execute();
        }
    }
}
