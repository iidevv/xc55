<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("QSL\MembershipProducts")
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    const SECONDS_IN_DAY = 86400;

    public function getItemsWithExpiredMembershipsInNextDays($daysNum)
    {
        return $this->defineQueryGetItemsWithExpiredMembershipsInNextDays($daysNum)->getResult();
    }

    protected function defineQueryGetItemsWithExpiredMembershipsInNextDays($daysNum)
    {
        // threshold in seconds before membership is canceled
        // the time difference between the membership cancellation time
        // and the current time must be less than the threshold for the email to be sent
        $threshold = static::SECONDS_IN_DAY * $daysNum;

        $qb = $this->createQueryBuilder('oi')
            ->linkInner('oi.order', 'o')
            ->linkInner('o.paymentStatus', 'ps')
            ->linkInner('oi.object', 'p')
            ->andWhere('p.appointmentMembership IS NOT NULL')
            ->andWhere('oi.customerMembershipExpirationSentDate IS NULL');

        $qb->andWhere('oi.customerMembershipApplied = :membershipAssigned')
            ->setParameter('membershipAssigned', true)
            ->andWhere('oi.customerMembershipUnassignDate - :time1 <= :threshold')
            ->andWhere('oi.customerMembershipUnassignDate - :time1 > 0')
            ->setParameter('time1', \XLite\Core\Converter::time())
            ->setParameter('threshold', $threshold);

        return $qb;
    }

    public function findAnyProfileAppliedMemberships(\XLite\Model\Profile $profile = null)
    {
        $qb = $this->createQueryBuilder('oi')
            ->linkInner('oi.order', 'o')
            ->linkInner('o.paymentStatus', 'ps')
            ->linkInner('oi.object', 'p')
            ->andWhere('p.appointmentMembership IS NOT NULL');

        if (
            $profile
            && $profile->isPersistent()
        ) {
            $qb->andWhere('o.orig_profile = :profile')
                ->setParameter('profile', $profile);
            // make sure membership is same as specified in order
            // this is change
           // $qb->linkInner('o.orig_profile', 'op')
           //     ->andWhere('op.membership = p.appointmentMembership');
        }

        $qb->andWhere(
            $qb->expr()->in(
                'ps.code',
                \XLite\Model\Order\Status\Payment::getPaidStatuses()
            )
        )
            ->andWhere('oi.customerMembershipApplied = :membershipApplied')
            ->setParameter('membershipApplied', true)
            ->andWhere('oi.customerMembershipUnassignDate >= :time')
            ->setParameter('time', \XLite\Core\Converter::time());

        return $qb->getResult();
    }

}