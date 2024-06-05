<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

/**
 * QbcOrderStatus repo
 */
class QbcOrderStatus extends \XLite\Model\Repo\ARepo
{
    /**
     * Get existing record
     *
     * @param integer|null $paymentStatus
     * @param integer|null $shippingStatus
     * @param integer|null $id
     *
     * @return object|null
     */
    public function getExistingRecord($paymentStatus, $shippingStatus, $id = null)
    {
        $qb = $this->createQueryBuilder('q')
            ->andWhere('q.paymentStatus = :paymentStatus')
            ->andWhere('q.shippingStatus = :shippingStatus')
            ->setParameter('paymentStatus', $paymentStatus)
            ->setParameter('shippingStatus', $shippingStatus);
        
        if ($id) {
            $qb->andWhere('q.id != :id')
            ->setParameter('id', $id);
        }
        
        return $qb->getSingleResult();
    }
}