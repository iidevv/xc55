<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    public function findOrdersForSkuvaultSync()
    {
        $syncOrdersFromNum = (int)Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_orders_from_id;

        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.skuvaultNotSync = :skuvaultNotSync OR o.skuvaultNotSync IS NULL')
            ->setParameter('skuvaultNotSync', \XLite\Model\Order::NOT_SYNC_NO)
            ->setMaxResults(100);

        if ($syncOrdersFromNum > 0) {
            $qb->andWhere('o.orderNumber >= :orderNumber')
                ->setParameter('orderNumber', $syncOrdersFromNum);
        }

        return $qb->getQuery()->getResult();
    }
}
