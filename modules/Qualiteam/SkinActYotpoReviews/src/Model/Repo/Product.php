<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    public function findProductsForUpdateAverageRatingAndVotesCount(int $start, int $limit)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.yotpo_id IS NOT NULL')
            ->setFrameResults($start, $limit)
            ->getQuery()
            ->getResult();
    }

    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            [$sort, $order] = $this->getSortOrderValue($value);
            if ($sort == 'r.rating') {
                $queryBuilder->addSelect('p.average_rating as rsm, p.votes_count as rates_count');
                $sort = 'rsm';
                $queryBuilder->addOrderBy($sort, $order);
                $sort = 'rates_count';
                $queryBuilder->addOrderBy($sort, 'DESC');
            } else {
                parent::prepareCndOrderBy($queryBuilder, $value);
            }
        }
    }
}