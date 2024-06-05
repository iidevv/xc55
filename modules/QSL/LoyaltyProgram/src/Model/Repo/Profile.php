<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated repository for Profile model.
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Repo\Profile
{
    /**
     * Calculate the total number of customers having reward points and the number of their points.
     * Returns an array of two values: the number of customers and the sum of their points.
     *
     * @return array
     */
    public function getRewardPointsStatistics()
    {
        $data = $this->defineRewardPointsStatisticsQuery()->getSingleResult();

        return (isset($data['users']) && isset($data['points']))
            ? [(int) $data['users'], (int) $data['points']]
            : [0, 0];
    }

    /**
     * Prepare query builder to retrieve statistics on reward points unused by customers.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineRewardPointsStatisticsQuery()
    {
        return $this->createPureQueryBuilder('p')
            ->select('COUNT(p.profile_id) as users')
            ->addSelect('SUM(p.rewardPoints) as points')
            ->leftJoin('p.order', 'o')
            ->andWhere('o.order_id IS NULL')
            ->andWhere('p.anonymous = :anonymous')
            ->andWhere('p.status = :status')
            ->andWhere('p.rewardPoints > :noRewardPoints')
            ->setParameter('anonymous', 0)
            ->setParameter('status', \XLite\Model\Profile::STATUS_ENABLED)
            ->setParameter('noRewardPoints', 0);
    }
}
