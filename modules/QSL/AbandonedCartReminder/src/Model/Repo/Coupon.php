<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\Model\Repo\Coupon
{
    /**
     * Available search parameters.
     */
    public const SEARCH_EXPIRED = 'expired';

    /**
     * Unlink all coupons from a cart.
     *
     * @param \XLite\Model\Order $cart Cart model
     *
     * @return void
     */
    public function unlinkAllFromCart(\XLite\Model\Order $cart)
    {
        $this->defineUnlinkAllFromCart($cart)->execute();
    }

    /**
     * Define query for unlinking all coupons from a cart.
     *
     * @param \XLite\Model\Order $cart Cart model
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineUnlinkAllFromCart(\XLite\Model\Order $cart)
    {
        $qb = $this->getQueryBuilder();

        return $qb->update($this->_entityName, 'p')
            ->set('p.abandonedCart', 'NULL')
            ->andWhere('p.abandonedCart = :cartId')
            ->setParameter('cartId', $cart->getOrderId());
    }

    /**
     * Search expired coupons.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndExpired(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $cnd = $value
                ? '(c.dateRangeEnd > 0 AND c.dateRangeEnd < :expired_before_date)'
                : '(c.dateRangeEnd < 1 OR c.dateRangeEnd >= :expired_before_date)';
            $queryBuilder->andWhere($cnd)
                ->setParameter('expired_before_date', \XLite\Core\Converter::time());
        }
    }

    /**
     * Deletes coupons expired before the date.
     *
     * @param int $timestamp Date
     *
     * @return int
     */
    public function deleteExpiredCoupons($timestamp)
    {
        return $this->prepareDeleteExpiredCoupons($timestamp)->execute();
    }

    /**
     * Prepare query builder for deleteExpiredCoupons() method
     *
     * @param int $timestamp Emails sent before this date will be deleted
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareDeleteExpiredCoupons($timestamp)
    {
        $q = $this->getQueryBuilder()
            ->delete($this->_entityName, 'c')
            ->andWhere('(c.dateRangeEnd > 0 AND c.dateRangeEnd < :expired_before_date)')
            ->setParameter('expired_before_date', (int) $timestamp);

        return $q;
    }
}
