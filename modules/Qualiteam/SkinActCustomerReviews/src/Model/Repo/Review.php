<?php


namespace Qualiteam\SkinActCustomerReviews\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\Model\Repo\Review
{
    protected function prepareCndOrderUseful(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            // most useful reviews goes first
            $queryBuilder->orderBy('r.useful', 'desc');
        }
    }

    protected function prepareCndOrderDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            // newest reviews goes first
            $queryBuilder->orderBy('r.additionDate', 'desc');
        }
    }
}