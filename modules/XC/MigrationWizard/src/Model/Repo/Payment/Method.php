<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * Payment method repository
 * @Extender\Mixin
 */
abstract class Method extends \XLite\Model\Repo\Payment\Method
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

        return $qb->andWhere($condition1)
                ->andWhere($condition2)
                ->setParameter('added', true)
                ->setParameter('enabled', true);
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

        return $qb->andWhere($condition1)
                ->andWhere($condition2)
                ->setParameter('added', true)
                ->setParameter('enabled', true);
    }
}
