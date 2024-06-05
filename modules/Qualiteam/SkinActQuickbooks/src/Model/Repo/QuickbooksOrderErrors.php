<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * The Quickbooks Orders Errors model repository
 */
class QuickbooksOrderErrors extends \XLite\Model\Repo\ARepo
{
    /**
     * Delete quickbooks orders errors
     * 
     * @param mixed $orderIds
     * 
     * @return void
     */
    public function deleteOrdersErrors($orderIds)
    {
        if (!empty($orderIds)) {
            if (!is_array($orderIds)) {
                $orderIds = [$orderIds];
            }
            $this->createQueryBuilder('qoe')
                ->andWhere('qoe.order_id in (:ids)')
                ->setParameter('ids', $orderIds)
                ->delete()
                ->getQuery()
                ->execute();
        }
    }
    
    /**
     * Delete all quickbooks orders errors
     * 
     * @return void
     */
    public function deleteAllOrdersErrors()
    {
        $this->createQueryBuilder('qoe')->delete()->getQuery()->execute();
    }
    
    /**
     * Check if order error record exists
     *
     * @param integer $order_id Order ID
     *
     * @return boolean
     */
    public function recordExists($order_id)
    {
        $count = $this->createPureQueryBuilder('qoe')
            ->select('COUNT(qoe.order_id)')
            ->andWhere('qoe.order_id = :order_id')
            ->setParameter('order_id', $order_id)
            ->getSingleScalarResult();

        return ($count > 0);
    }
    
    /**
     * Get not synced order ids (with errors) by specified profile
     * 
     * @param integer $profileId
     * 
     * @return array
     */
    public function getOrderIdsByProfile($profileId)
    {
        $result = [];
        
        if (!empty($profileId)) {
            
            $qb = $this->createQueryBuilder('qoe')
                ->select('IDENTITY(qoe.order_id)');
            
            $qb->innerJoin('XLite\Model\Order', 'o', 'WITH', 'o.order_id = qoe.order_id')
                ->where('o.orig_profile = :profile')
                ->setParameter('profile', $profileId);
            
            $result = $qb->getQuery()->getScalarResult();
        }
        
        return array_column($result, 1);
    }
    
    /**
     * Get not synced order ids (with errors) by specified product/variant
     * 
     * @param integer $productId
     * @param integer $variantId
     * 
     * @return array
     */
    public function getOrderIdsByProduct($productId, $variantId = 0)
    {
        $result = [];
        
        if (!empty($productId)) {
            
            $xcVariantsModule = class_exists('XC\ProductVariants\Main');
            
            $qb = $this->createQueryBuilder('qoe')
                ->select('IDENTITY(qoe.order_id)');
            
            $qb->innerJoin(
                'XLite\Model\Order',
                'o',
                'WITH',
                'o.order_id = qoe.order_id'
            );
            
            $orderItemCondition = ['oi.order = o.order_id'];
            $orderItemCondition[] = 'oi.object = :product';
            
            if ($xcVariantsModule) {
                
                $orderItemCondition[] = 'oi.variant = :variant';
            }
            
            $qb->innerJoin(
                'XLite\Model\OrderItem',
                'oi',
                'WITH',
                implode(' AND ', $orderItemCondition)
            );
            
            $qb->setParameter('product', $productId);
            $qb->setParameter('variant', $variantId);
            
            $result = $qb->getQuery()->getScalarResult();
        }
        
        return array_column($result, 1);
    }
    
    /**
     * Get not synced order numbers
     * 
     * @return array
     */
    public function getErrorOrderNumbers()
    {
        $result = [];
        
        $qb = $this->createQueryBuilder('qoe')
            ->select('o.orderNumber');
        
        $qb->innerJoin(
            'XLite\Model\Order',
            'o',
            'WITH',
            'o.order_id = qoe.order_id'
        );
        
        $qb->andWhere('qoe.send = :send')
            ->setParameter('send', 0);
        
        $result = $qb->getResult();
        
        return array_column($result, 'orderNumber');
    }
    
    /**
     * Mark quickbooks orders errors as sent
     * 
     * @return void
     */
    public function markOrdersErrorsAsSent()
    {
        $query = 'UPDATE ' . $this->getTableName()
               . ' SET send = ? WHERE send = 0';
        Database::getEM()->getConnection()->executeUpdate(
            $query,
            [Converter::time()]
        );
    }
}