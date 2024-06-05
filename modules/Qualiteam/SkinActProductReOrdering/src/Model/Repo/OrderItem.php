<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\Model\Repo;

use XLite\Model\Product;
use XLite\Model\Profile;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    public function getLastOrderItem(Profile $profile, Product $product)
    {
        return $this->createPureQueryBuilder()
            ->leftJoin('o.order', 'ord')
            ->andWhere('o.object = :currentProduct')
            ->andWhere('ord.orig_profile = :currentProfile')
            ->andWhere('ord.orderNumber IS NOT NULL')
            ->setParameter('currentProfile', $profile)
            ->setParameter('currentProduct', $product)
            ->orderBy('o.item_id', 'DESC')
            ->getSingleResult();
    }
}