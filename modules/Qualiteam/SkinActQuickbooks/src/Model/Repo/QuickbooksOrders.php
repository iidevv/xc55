<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

/**
 * The Order model repository
 */
class QuickbooksOrders extends \XLite\Model\Repo\ARepo
{
    /**
     * Delete quickbooks orders
     * 
     * @param mixed $orderIds
     * 
     * @return void
     */
    public function deleteOrders($orderIds)
    {
        if (!empty($orderIds)) {
            if (!is_array($orderIds)) {
                $orderIds = [$orderIds];
            }
            $this->createQueryBuilder('qo')
                ->andWhere('qo.order_id in (:ids)')
                ->setParameter('ids', $orderIds)
                ->delete()
                ->getQuery()
                ->execute();
        }
    }
    
    /**
     * Check if order record exists
     *
     * @param integer $order_id Order ID
     *
     * @return boolean
     */
    public function recordExists($order_id)
    {
        $count = $this->createPureQueryBuilder('qo')
            ->select('COUNT(qo.order_id)')
            ->andWhere('qo.order_id = :order_id')
            ->setParameter('order_id', $order_id)
            ->getSingleScalarResult();

        return ($count > 0);
    }
    
    /**
     * Get profile ID from order
     *
     * @param integer $order_id Order ID
     *
     * @return string|null
     */
    public function getProfileIdFromOrder($order_id)
    {
        $qb = $this->createPureQueryBuilder('qo');
        $qb->select('IDENTITY(o.orig_profile)');
        $qb->innerJoin(
            'XLite\Model\Order', 'o', 'WITH',
            "o.order_id = qo.order_id"
        )->andWhere('qo.order_id = :order_id')
            ->setParameter('order_id', $order_id);
        
        return $qb->getSingleScalarResult();
    }
}