<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Model\Repo;

class ExtraCouponsAndDiscounts extends \XLite\Model\Repo\ARepo
{
    const P_ENABLED = 'enabled';

    public function findDuplicatesCount($code, $currentCouponId = null)
    {
        return $this->defineFindDuplicatesQuery($code, $currentCouponId)->count();
    }

    protected function defineFindDuplicatesQuery($code, $currentCouponId = null)
    {
        $queryBuilder = $this->createPureQueryBuilder('e')
            ->andWhere('BINARY(e.coupon_code) = :code')
            ->setParameter('code', $code);

        if ($currentCouponId) {
            $queryBuilder->andWhere('e.id != :eid')
                ->setParameter('eid', $currentCouponId);
        }

        return $queryBuilder;
    }

    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkLeft('e.coupon', 'c')
                ->andWhere('c.enabled = :enableExtraCoupon')
                ->setParameter('enableExtraCoupon', $value);
        }
    }
}