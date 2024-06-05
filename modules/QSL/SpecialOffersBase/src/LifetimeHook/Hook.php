<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\SpecialOffersBase\LifetimeHook;

use CDev\SimpleCMS\Model\Menu;
use QSL\SpecialOffersBase\Main;

final class Hook
{
    public function onRebuild(): void
    {
        Main::updateOfferTypes();

        if (class_exists(Menu::class)) {
            Main::addSimpleCMSMenuLink();
        }

        \XLite\Core\Database::getEM()->flush();
    }

    public function onUpgradeTo5500(): void
    {
        $this->updateProviders();
        \XLite\Core\Database::getEM()->flush();
    }

    private function updateProviders(): void
    {
        $repo = \XLite\Core\Database::getRepo(\QSL\SpecialOffersBase\Model\OfferType::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('ot');

            $qb
                ->update(\QSL\SpecialOffersBase\Model\OfferType::class, 'ot')
                ->set('ot.processorClass', "REPLACE(ot.processorClass, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('ot.processorClass', "'\\\XLite%'"))
                ->execute();

            $qb = $repo->createPureQueryBuilder('ot');

            $qb
                ->update(\QSL\SpecialOffersBase\Model\OfferType::class, 'ot')
                ->set('ot.viewModelClass', "REPLACE(ot.viewModelClass, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('ot.viewModelClass', "'\\\XLite%'"))
                ->execute();
        }
    }
}
