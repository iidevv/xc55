<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs;

use Includes\Utils\Module\Manager;
use XLite\Core\Database;
use XLite\Model\Product\GlobalTab;
use XLite\Model\Product\GlobalTabProvider;

abstract class Main extends \XLite\Module\AModule
{
    public const MODULE_ID = 'XC-CustomProductTabs';

    public static function removeUninstalledModulesTabs()
    {
        $qb = Database::getRepo('XLite\Model\Product\GlobalTab')->createQueryBuilder();

        $alias = $qb->getMainAlias();
        $qb->andWhere("{$alias}.service_name IS NOT NULL");

        $moduleTabs = $qb->getResult();

        /** @var GlobalTab $tab */
        foreach ($moduleTabs as $tab) {
            /** @var GlobalTabProvider $provider */
            foreach ($tab->getProviders() as $provider) {
                if ($provider->getCode() === GlobalTabProvider::PROVIDER_CORE) {
                    continue;
                }

                $module = Manager::getRegistry()->getModule($provider->getCode());
                if (!$module) {
                    $tab->getProviders()->removeElement($provider);
                    Database::getEM()->remove($provider);
                }

                if ($tab->getProviders()->isEmpty()) {
                    Database::getEM()->remove($tab);
                }
            }
        }

        Database::getEM()->flush();
    }
}
