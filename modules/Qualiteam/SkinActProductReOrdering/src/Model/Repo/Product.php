<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    const RE_ORDER_PROFILE_ID = 'reOrderProfileId';

    protected function prepareCndReOrderProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder
                ->leftJoin('p.order_items', 'oi')
                ->leftJoin('oi.order', 'ord')
                ->andWhere('ord.orig_profile = :orig_profile')
                ->andWhere('ord.orderNumber IS NOT NULL')
                ->setParameter('orig_profile', $value)
                ->groupBy('oi.object_id');
        }
    }
}