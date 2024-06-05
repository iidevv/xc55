<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksCustomers;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrders;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrderErrors;
use Qualiteam\SkinActQuickbooks\Model\QbcOrderStatus;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;

/**
 * The Order model repository
 * 
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    public const SEARCH_QUICKBOOKS_ORDERS = 'quickbooks_orders';
    public const SEARCH_QUICKBOOKS_ORDER_ERRORS = 'quickbooks_order_errors';
    
    /**
     * prepareCndQuickbooksOrders
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndQuickbooksOrders(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->innerJoin(
            'Qualiteam\SkinActQuickbooks\Model\QuickbooksOrders',
            'qo',
            'WITH',
            'qo.order_id = o.order_id'
        );
    }
    
    /**
     * prepareCndQuickbooksOrderErrors
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndQuickbooksOrderErrors(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->innerJoin(
            'Qualiteam\SkinActQuickbooks\Model\QuickbooksOrderErrors',
            'qoe',
            'WITH',
            'qoe.order_id = o.order_id'
        );
    }
    
    /**
     * Get order ids for sync
     * 
     * @return array
     */
    public function getOrderIdsForSync()
    {
        $qb = $this->createPureQueryBuilder('o');
        
        $qb->select('o.order_id');
        $qb->leftJoin(QuickbooksOrders::class, 'qo', 'WITH', 'qo.order_id = o.order_id')
            ->andWhere($qb->expr()->orX('qo.order_id IS NULL', "qo.quickbooks_txnid = ''"));
        
        $qb->leftJoin(QuickbooksOrderErrors::class, 'qoe', 'WITH', 'qoe.order_id = o.order_id')
            ->andWhere('qoe.order_id IS NULL');
        
        $qb->andWhere("o.qbc_ignore != 'Y'");
        
        $fromOrder = intval(Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_orders_start_id);
        $qb->andWhere($qb->expr()->gte('o.orderNumber', $fromOrder));
        
        $statuses = Database::getRepo(QbcOrderStatus::class)->findAll();
        
        if ($statuses) {
            $statusesWhere = $qb->expr()->orX();
            foreach ($statuses as $status) {
                $statusWhere = $qb->expr()->andX();
                $statusWhere->add(
                    $qb->expr()->eq('o.paymentStatus', $status->getPaymentStatus()->getId())
                );
                $statusWhere->add(
                    $qb->expr()->eq('o.shippingStatus', $status->getShippingStatus()->getId())
                );
                $statusesWhere->add($statusWhere);
            }
            $qb->andWhere($statusesWhere);
        }
        
        $qb->setMaxResults(QuickbooksConnector::$queryLimit);
        
        return $qb->getQuery()->getSingleColumnResult();
    }
    
    /**
     * Get profile ids for sync
     * 
     * @return array
     */
    public function getProfileIdsForSync()
    {
        $qb = $this->createPureQueryBuilder('o');
        
        $qb->select('IDENTITY(o.orig_profile)')->distinct();
        
        $qb->leftJoin(QuickbooksCustomers::class, 'qc', 'WITH', 'qc.profile_id = o.orig_profile')
            ->andWhere(
                $qb->expr()->orX(
                    'qc.profile_id IS NULL',
                    "qc.quickbooks_listid = ''"
                )
            );
        
        $orderIds = $this->getOrderIdsForSync();
        if (empty($orderIds)) $orderIds = [0];
        $qb->andWhere($qb->expr()->in('o.order_id', $orderIds));
        
        $qb->setMaxResults(QuickbooksConnector::$queryLimit);
        
        return $qb->getQuery()->getSingleColumnResult();
    }
    
    /**
     * Check if a customer is already synced
     * 
     * @param integer $profileId
     * 
     * @return boolean
     */
    public function checkProfileSynced($profileId)
    {
        $result = false;
        
        if ($profileId) {
            
            $qb = $this->createPureQueryBuilder('o');
        
            $qb->select('IDENTITY(o.orig_profile)')->distinct();

            $qb->leftJoin(
                QuickbooksCustomers::class,
                'qc',
                'WITH',
                    $qb->expr()->andX(
                        'qc.profile_id = o.orig_profile',
                        'qc.profile_id = :profile_id'
                    )
                )
                ->setParameter('profile_id', $profileId)
                ->andWhere('qc.profile_id IS NOT NULL')
                ->setMaxResults(1);
        }
        
        return ($qb->getQuery()->getOneOrNullResult() > 0);
    }
}