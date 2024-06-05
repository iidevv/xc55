<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\CustomProductTabs\LifetimeHook;

use XC\CustomProductTabs\Main;
use XC\CustomProductTabs\Model\Product\CustomGlobalTab;
use XLite\Core\Database;

final class Hook
{
    public function onInstall(): void
    {
        $queryBuilder = Database::getEM()->createQueryBuilder();
        $queryBuilder->update('XLite\Model\Product\GlobalTab', 'gt')
            ->set('gt.enabled', 1)
            ->getQuery()
            ->execute();

        $queryBuilder = Database::getEM()->createQueryBuilder();
        $queryBuilder->update('XC\CustomProductTabs\Model\Product\Tab', 'pt')
            ->set('pt.enabled', 1)
            ->getQuery()
            ->execute();
    }

    public function onRebuild(): void
    {
        $qb = Database::getRepo('XLite\Model\Product\GlobalTab')->createQueryBuilder();

        $alias = $qb->getMainAlias();
        $qb->addSelect('COUNT(psa.id) as HIDDEN aliases_count')
            ->leftJoin("{$alias}.product_specific_aliases", 'psa')
            ->andWhere("{$alias}.service_name IS NOT NULL")
            ->having('aliases_count < :products_count')
            ->groupBy("{$alias}.id")
            ->setParameter('products_count', Database::getRepo('XLite\Model\Product')->count());

        foreach ($qb->getResult() as $globalTab) {
            Database::getRepo('XLite\Model\Product\GlobalTab')->createGlobalTabAliases($globalTab);
        }

        Main::removeUninstalledModulesTabs();
    }

    public function onUpgradeTo5500(): void
    {
        $customGlobalTabs = Database::getRepo('XC\CustomProductTabs\Model\Product\CustomGlobalTab')->findAll();

        foreach ($customGlobalTabs as $customGlobalTab) {
            /** @var CustomGlobalTab $customGlobalTab */
            $customGlobalTab->assignModule();
        }

        Database::getEM()->flush();
    }
}
