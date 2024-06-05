<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated repository for Order model.
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * Calculate the total number of redeemed reward points and the total sum of awarded discounts.
     * Returns an array of two values: the number of points and the sum of the total discount.
     *
     * @return array
     */
    public function getRedeemStatistics()
    {
        $data = $this->defineRedeemStatisticsQuery()->getSingleResult();

        return (isset($data['points']) && isset($data['discount']))
            ? [(int) $data['points'], abs((float) $data['discount'])]
            : [0, 0];
    }

    /**
     * Prepare query builder to retrieve statistics on redeemed points and the given total discount.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineRedeemStatisticsQuery()
    {
        $qb = $this->createQueryBuilder('o')
            ->select('SUM(o.redeemedPoints) as points')
            ->addSelect('SUM(s.value) as discount');

        $qb->innerJoin('o.surcharges', 's')
            ->andWhere($qb->expr()->in('s.code', \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getRewardPointsModifierCodes()));

        $qb->innerJoin('o.paymentStatus', 'ps')
            ->andWhere($qb->expr()->in('ps.code', $this->getRedeemOrderStatuses()));

        return $qb;
    }

    /**
     * Get the list of order statuses for which redeemed reward points were redeemed _actually_.
     *
     * @return array
     */
    protected function getRedeemOrderStatuses()
    {
        return [
            \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
            \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
            \XLite\Model\Order\Status\Payment::STATUS_PAID,
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
        ];
    }
}
