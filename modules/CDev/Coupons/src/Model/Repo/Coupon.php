<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

class Coupon extends \XLite\Model\Repo\ARepo
{
    use ExecuteCachedTrait;

    // {{{ Find duplicates

    /**
     * Find duplicates
     *
     * @param string                                  $code          Code
     * @param \CDev\Coupons\Model\Coupon $currentCoupon Current coupon OPTIONAL
     *
     * @return array
     */
    public function findDuplicates($code, \CDev\Coupons\Model\Coupon $currentCoupon = null)
    {
        return $this->defineFindDuplicatesQuery($code, $currentCoupon)->getResult();
    }

    /**
     * Define query for findDuplicates()
     *
     * @param string                                  $code          Code
     * @param \CDev\Coupons\Model\Coupon $currentCoupon Current coupon OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicatesQuery($code, \CDev\Coupons\Model\Coupon $currentCoupon = null)
    {
        $queryBuilder = $this->createPureQueryBuilder('c')
            ->andWhere('BINARY(c.code) = :code')
            ->setParameter('code', $code);

        if ($currentCoupon) {
            $queryBuilder->andWhere('c.id != :cid')
                ->setParameter('cid', $currentCoupon->getId());
        }

        return $queryBuilder;
    }

    // }}}

    // {{{ Find by code

    /**
     * Find duplicates
     *
     * @param string $code Code
     *
     * @return null|\XLite\Model\AEntity
     */
    public function findOneByCode($code)
    {
        return $this->defineFindOneByCode($code)->getSingleResult();
    }

    /**
     * Define query for findDuplicates()
     *
     * @param string $code Code
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByCode($code)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('BINARY(c.code) = :code')
            ->setParameter('code', $code);

        return $queryBuilder;
    }

    // }}}

    public function findAllProductSpecific()
    {
        return $this->executeCachedRuntime(function () {
            $qb = $this->createQueryBuilder('c')
                ->andWhere('c.specificProducts = :specificProducts')
                ->setParameter('specificProducts', true);

            return $qb->getResult();
        });
    }
}
