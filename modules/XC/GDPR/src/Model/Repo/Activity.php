<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Model\Repo;

class Activity extends \XLite\Model\Repo\ARepo
{
    public const PARAM_TYPE = 'type';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $alias = $this->getMainAlias($queryBuilder);

        $queryBuilder->andWhere("{$alias}.type = :type")
            ->setParameter('type', $value);
    }
}
