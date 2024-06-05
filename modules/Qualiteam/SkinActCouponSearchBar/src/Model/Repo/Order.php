<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCouponSearchBar\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    protected function prepareCndCouponId(QueryBuilder $queryBuilder, $value = null)
    {
        if ($value > 0) {
            $queryBuilder->linkInner('o.usedCoupons', 'uc');
            $queryBuilder->andWhere('uc.coupon = :couponId');
            $queryBuilder->setParameter('couponId', $value);
        }

    }

}