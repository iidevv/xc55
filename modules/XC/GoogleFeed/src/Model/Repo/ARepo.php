<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XC\GoogleFeed\Core\EventListener\FeedGeneration;

/**
 * Abstract repository
 * @Extender\Mixin
 */
class ARepo extends \XLite\Model\Repo\ARepo
{
    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getFeedGenerationIterator($position = 0)
    {
        return $this->defineFeedGenerationQueryBuilder($position)
            ->setMaxResults(FeedGeneration::CHUNK_LENGTH)
            ->iterate();
    }

    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \Doctrine\ORM\QueryBuilder|\XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFeedGenerationQueryBuilder($position)
    {
        return $this->createPureQueryBuilder()
            ->setFirstResult($position);
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \Doctrine\ORM\QueryBuilder|\XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForFeedGenerationQuery()
    {
        $qb = $this->defineFeedGenerationQueryBuilder(0)
            ->setMaxResults(1000000000);

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Count items for sitemap generation
     *
     * @return integer
     */
    public function countForFeedGeneration()
    {
        return (int)$this->defineCountForFeedGenerationQuery()->getSingleScalarResult();
    }
}
