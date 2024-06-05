<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Model\Repo;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Coupon extends \CDev\Coupons\Model\Repo\Coupon
{
    public function findDuplicatesProMembershipCouponsCount($code)
    {
        return $this->defineFindDuplicatesProMembershipCouponsQuery($code)->count();
    }

    protected function defineFindDuplicatesProMembershipCouponsQuery($code)
    {
        $queryBuilder = $this->createPureQueryBuilder('c')
            ->andWhere('BINARY(c.code) = :code')
            ->andWhere('c.extraCoupon IS NULL')
            ->setParameter('code', $code);

        return $queryBuilder;
    }
}