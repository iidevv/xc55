<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    public const SEARCH_ORIG_PROFILE_ID = 'origProfileId';

    protected function prepareCndOrigProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkLeft('p.order_items', 'ordi');
            $queryBuilder->linkLeft('ordi.order', 'ord');

            $queryBuilder->andWhere('ord.orig_profile = :origProfileId')
                ->andWhere('ord.orderNumber IS NOT NULL')
                ->setParameter('origProfileId', $value);
        }
    }
}