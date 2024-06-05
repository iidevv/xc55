<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model\Repo;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class order tracking number
 * @Extender\Mixin
 */
class OrderTrackingNumber extends \XLite\Model\Repo\OrderTrackingNumber
{
    /**
     * Get all not sync tracking numbers
     *
     * @return array
     */
    public function getAllTrackingsForAftershipSync($start, $limit): array
    {
        return $this->createQueryBuilder('otn')
            ->andWhere('otn.aftership_sync = :aftershipSyncFalse')
            ->andWhere('otn.value IS NOT NULL')
            ->andWhere('otn.value != :emptyValue')
            ->andWhere('otn.aftership_courier_name IS NOT NULL')
            ->andWhere('otn.aftership_courier_name != :emptyValueCourierName')
            ->setParameter('aftershipSyncFalse', false)
            ->setParameter('emptyValue', '')
            ->setParameter('emptyValueCourierName', '')
            ->setFrameResults($start, $limit)
            ->getResult();
    }
}