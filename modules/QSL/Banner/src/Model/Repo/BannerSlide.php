<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model\Repo;

/**
 * Banner repository
 *
 */
class BannerSlide extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */

    public const P_ENABLED = 'enabled';

    /**
     * Default 'order by' field name
     *
     * @var   string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Get last order by
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getLastOrderBy()
    {
        return $this->findLastOrderBy()->getSingleScalarResult();
    }

    /**
     * Find last order by
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function findLastOrderBy()
    {
        return $this->createQueryBuilder()->select('MAX(b.position)');
    }

    // {{{ Search

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.enabled = true');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param $value
     */
    protected function prepareCndBanner(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.banner = :banner')->setParameter('banner', $value);
        }
    }
}
