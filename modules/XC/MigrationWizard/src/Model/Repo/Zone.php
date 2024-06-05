<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Zone repository
 * @Extender\Mixin
 */
abstract class Zone extends \XLite\Model\Repo\Zone
{
    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForRemoveDataQuery()
    {
        $qb = parent::defineCountForRemoveDataQuery();

        $condition = $qb->getMainAlias() . '.is_default = :is_default';

        return $qb->andWhere($condition)
            ->setParameter('is_default', false);
    }

    /**
     * Define remove data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRemoveDataQueryBuilder($position)
    {
        $qb = parent::defineRemoveDataQueryBuilder($position);

        $condition = $qb->getMainAlias() . '.is_default = :is_default';

        return $qb->andWhere($condition)
            ->setParameter('is_default', false);
    }
}
