<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model\Repo;

use XLite\Model\Repo\ARepo;

/**
 * Metadata - repo
 */
class Metadata extends ARepo
{
    /**
     * Get by external data
     *
     * @param string $entityType
     * @param array $externalData
     *
     * @return object|null
     */
    public function getByExternalData(string $entityType, array $externalData)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.entityType = :entityType')
            ->andWhere('m.externalData = :externalData')
            ->setParameter('entityType', $entityType)
            ->setParameter('externalData', serialize($externalData))
            ->getSingleResult();
    }
}
