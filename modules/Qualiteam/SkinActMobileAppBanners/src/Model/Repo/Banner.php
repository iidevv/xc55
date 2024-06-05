<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\Model\Repo;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Banner extends \QSL\Banner\Model\Repo\Banner
{
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        $qb = parent::createQueryBuilder($alias, $indexBy);

        if (\XLite::isAdminZone()) {
            return $qb;
        }

        if (!\XLite::inGraphQLContext()) {
            $qb->andWhere($qb->getMainAlias() . '.forMobileOnly != 1');
        }

        return $qb;
    }

    protected function prepareCndForMobileOnly(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere($queryBuilder->getMainAlias() . '.forMobileOnly = 1');
        }
    }

}