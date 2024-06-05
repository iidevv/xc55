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
class Coupon extends \CDev\Coupons\Model\Repo\Coupon
{

    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (isset($value[0]) && $value[0] === 'code1') {

            $queryBuilder->addSelect("REGEXP(c.code, '^[0-9]+$') as isNumeric");
            $queryBuilder->addSelect("CAST(c.code as SIGNED) * CAST(REGEXP(c.code, '^[0-9]+$') as SIGNED) as numericCode");

            if ($value[1] === 'asc') {
                $queryBuilder->addOrderBy('isNumeric', 'desc');
                $queryBuilder->addOrderBy('numericCode', 'asc');
                $queryBuilder->addOrderBy('c.code', 'asc');
            }

            if ($value[1] === 'desc') {
                $queryBuilder->addOrderBy('isNumeric', 'asc');
                $queryBuilder->addOrderBy('numericCode', 'desc');
                $queryBuilder->addOrderBy('c.code', 'desc');
            }

            return;
        }

        return parent::prepareCndOrderBy($queryBuilder, $value);
    }

    protected function prepareCndSubstr(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere('c.code LIKE :substring')
                ->setParameter('substring', '%' . $value . '%');
        }
    }

}