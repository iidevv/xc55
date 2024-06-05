<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The OrderItem model repository extension
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    /**
     * Returns the top sellers count (used on the dashboard)
     *
     * @param integer              $productId Product Id
     * @param \XLite\Model\Profile $profile   Customer profile
     *
     * @return boolean
     */
    public function countItemsPurchasedByCustomer($productId, $profile)
    {
        return $profile && $profile->getProfileId()
            ? 0 < $this->defineCountItemsPurchasedByCustomer($productId, $profile)->getSingleScalarResult()
            : 0;
    }

    /**
     * Prepare query for countItemsPurchasedByCustomer() method
     *
     * @param integer              $productId Product Id
     * @param \XLite\Model\Profile $profile   Customer profile
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountItemsPurchasedByCustomer($productId, $profile)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->select('COUNT(i.item_id)')
            ->innerJoin('i.object', 'p')
            ->innerJoin('i.order', 'o')
            ->innerJoin('o.orig_profile', 'profile')
            ->innerJoin('o.paymentStatus', 'ps')
            ->andWhere('p.product_id = :productId')
            ->andWhere('profile.profile_id = :profileId')
            ->andWhere($qb->expr()->in('ps.code', \XLite\Model\Order\Status\Payment::getPaidStatuses()))
            ->setParameter('productId', $productId)
            ->setParameter('profileId', $profile->getProfileId());

        return $qb;
    }
}
