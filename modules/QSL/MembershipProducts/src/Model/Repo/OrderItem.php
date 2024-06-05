<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Order item model repository
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    /**
     * Check and revert assigned memberships
     *
     * @return integer
     */
    public function checkAndRevertAssignedMemberships()
    {
        $count = 0;
        /** @var \QSL\MembershipProducts\Model\OrderItem $item */
        foreach ($this->getItemsWithExpiredMemberships() as $item) {
            if (!$item->resetCustomerMembership()) {
                // Force off - if profile deleted or profile has another membership or something else
                $item->setCustomerMembershipApplied(false);
            }
            $count++;
        }

        if ($count > 0) {
            \XLite\Core\Database::getEM()->flush();
        }

        return $count;
    }

    /**
     * Check and assign unassigned memberships
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return integer
     */
    public function checkAndAssignUnassignedMemberships(\XLite\Model\Profile $profile = null)
    {
        $count = 0;
        /** @var \QSL\MembershipProducts\Model\OrderItem $item */
        foreach ($this->getItemsWithUnassignedMemberships($profile) as $item) {
            if ($item->canApplyMembershipToCustomer()) {
                $item->applyMembershipToCustomer();
                $count++;
            }
        }

        if ($count > 0) {
            \XLite\Core\Database::getEM()->flush();
        }

        return $count;
    }

    /**
     * Get items with applied and opened (not unassigned) memberships by profile
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XLite\Model\OrderItem[]
     */
    public function findItemsWithOpenedAppliedMemberships(\XLite\Model\Profile $profile = null)
    {
        return $this->defineQueryFindItemsWithOpenedAppliedMemberships($profile)->getResult();
    }

    /**
     * Get items applied profile membership expiration date
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XLite\Model\OrderItem[]
     */
    public function findItemsAppliedProfileMembershipExpirationDate(\XLite\Model\Profile $profile = null)
    {
        return $this->defineQueryFindItemsAppliedProfileMembershipExpirationDate($profile)->getResult();
    }

    /**
     * Get order items with expired memberships
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getItemsWithExpiredMemberships()
    {
        return $this->defineQueryGetItemsWithExpiredMemberships()->getResult();
    }

    /**
     * Get order items with unassigned memberships
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getItemsWithUnassignedMemberships(\XLite\Model\Profile $profile = null)
    {
        return $this->defineQueryGetItemsWithUnassignedMemberships($profile)->getResult();
    }

    /**
     * Define query builder for 'getItemsWithExpiredMemberships' method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryGetItemsWithExpiredMemberships()
    {
        $qb = $this->createQueryBuilder('oi')
            ->linkInner('oi.order', 'o')
            ->linkInner('o.paymentStatus', 'ps')
            ->linkInner('oi.object', 'p')
            ->andWhere('p.appointmentMembership IS NOT NULL');

        $qb->andWhere('oi.customerMembershipApplied = :membershipAssigned')
            ->setParameter('membershipAssigned', true)
            ->andWhere('oi.customerMembershipUnassignDate <= :time')
            ->setParameter('time', \XLite\Core\Converter::time());

        return $qb;
    }

    /**
     * Define query builder for 'getItemsWithUnassignedMemberships' method
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryGetItemsWithUnassignedMemberships(\XLite\Model\Profile $profile = null)
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
        }

        $qb->andWhere(
            $qb->expr()->in(
                'ps.code',
                \XLite\Model\Order\Status\Payment::getPaidStatuses()
            )
        )
            ->andWhere('oi.customerMembershipApplied = :membershipUnassigned')
            ->setParameter('membershipUnassigned', false)
            ->andWhere('oi.customerMembershipAssignDate = 0')
            ->andWhere('oi.customerMembershipUnassignDate = 0');

        return $qb;
    }

    /**
     * Define query builder for 'findItemsWithOpenedAppliedMemberships' method
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryFindItemsWithOpenedAppliedMemberships(\XLite\Model\Profile $profile = null)
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
        }

        $qb->andWhere(
            $qb->expr()->in(
                'ps.code',
                \XLite\Model\Order\Status\Payment::getPaidStatuses()
            )
        )
            ->andWhere('oi.customerMembershipApplied = :membershipApplied')
            ->setParameter('membershipApplied', false);

        return $qb;
    }

    /**
     * Define query builder for 'findItemsAppliedProfileMembershipExpirationDate' method
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryFindItemsAppliedProfileMembershipExpirationDate(\XLite\Model\Profile $profile = null)
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
            $qb->linkInner('o.orig_profile', 'op')
                ->andWhere('op.membership = p.appointmentMembership');
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

        return $qb;
    }
}
