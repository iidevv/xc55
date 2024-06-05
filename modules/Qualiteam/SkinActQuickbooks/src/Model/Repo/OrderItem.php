<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;

/**
 * The "order_item" model repository
 * 
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    /**
     * Get products for sync
     * 
     * @return array
     */
    public function getProductsForSync()
    {
        $qb = $this->createPureQueryBuilder('oi');
        
        $qb->select('
            DISTINCT(oi.sku) AS sku,
            IDENTITY(oi.object) AS product_id,
            IDENTITY(oi.variant) AS variant_id,
            qp.quickbooks_fullname,
            qp.quickbooks_listid,
            IDENTITY(qp.product_id) AS check
        ');
        
        $qb->leftJoin(
            QuickbooksProducts::class,
            'qp',
            'WITH',
            'qp.product_id = oi.object AND qp.variant_id = IFNULL(oi.variant, 0)'
            )
            ->where(
                $qb->expr()->orX(
                    'qp.product_id IS NULL',
                    "qp.quickbooks_listid = ''"
                )
            );
        
        $orderIds = Database::getRepo('XLite\Model\Order')->getOrderIdsForSync();
        if (empty($orderIds)) $orderIds = [0];
        $qb->andWhere($qb->expr()->in('oi.order', $orderIds));
        
        $qb->setMaxResults(QuickbooksConnector::$queryLimit);
        
        return $qb->getQuery()->getArrayResult();
    }
}