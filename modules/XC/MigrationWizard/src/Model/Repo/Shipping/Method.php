<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping method repository
 * @Extender\Mixin
 */
abstract class Method extends \XLite\Model\Repo\Shipping\Method
{
    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForRemoveDataQuery()
    {
        $qb = parent::defineCountForRemoveDataQuery();

        $condition1 = $qb->getMainAlias() . '.added = :added';
        $condition2 = $qb->getMainAlias() . '.enabled = :enabled';
        $condition3 = $qb->getMainAlias() . '.moduleName <> :moduleName';
        $condition4 = $qb->getMainAlias() . '.processor = :processor';

        return $qb->andWhere($condition1 . ' AND ' . $condition2 . ' AND ' . $condition3)
                ->orWhere($condition1 . ' AND ' . $condition4)
                ->setParameter('added', true)
                ->setParameter('enabled', true)
                ->setParameter('moduleName', '')
                ->setParameter('processor', 'offline');
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

        $condition1 = $qb->getMainAlias() . '.added = :added';
        $condition2 = $qb->getMainAlias() . '.enabled = :enabled';
        $condition3 = $qb->getMainAlias() . '.moduleName <> :moduleName';
        $condition4 = $qb->getMainAlias() . '.processor = :processor';

        return $qb->andWhere($condition1 . ' AND ' . $condition2 . ' AND ' . $condition3)
                ->orWhere($condition1 . ' AND ' . $condition4)
                ->setParameter('added', true)
                ->setParameter('enabled', true)
                ->setParameter('moduleName', '')
                ->setParameter('processor', 'offline');
    }
}
