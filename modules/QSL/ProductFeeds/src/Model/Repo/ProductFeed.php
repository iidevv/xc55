<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model\Repo;

/**
 * Repository class for the ProductFeed model.
 */
class ProductFeed extends \XLite\Model\Repo\ARepo
{
    /**
     * Find a not finished feed.
     *
     * @return \QSL\ProductFeeds\Model\ProductFeed
     */
    public function findOneNotFinishedFeed()
    {
        return $this->defineNotFinishedFeedQuery()
            ->setMaxResults(1)
            ->getSingleResult();
    }

    /**
     * Find all not finished feeds.
     *
     * @return array
     */
    public function findNotFinishedFeeds()
    {
        return $this->defineNotFinishedFeedQuery()->getResult();
    }

    /**
     * Find all feeds which are not being regenerated.
     */
    public function findFinishedFeeds()
    {
        return $this->defineFinishedFeedQuery()->getResult();
    }

    /**
     * Define the query for findOneNotFinishedFeed() method.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineNotFinishedFeedQuery()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.startedDate > f.finishedDate')
            ->andWhere('f.enabled = :enabled')->setParameter('enabled', true)
            ->addOrderBy('f.startedDate', 'ASC');
    }

    /**
     * Define the query for findFinishedFeeds() method.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFinishedFeedQuery()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.startedDate > 0')
            ->andWhere('f.startedDate <= f.finishedDate')
            ->andWhere('f.enabled = :enabled')->setParameter('enabled', true)
            ->addOrderBy('f.finishedDate', 'ASC');
    }
}
