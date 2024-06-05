<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model\Repo;

use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Class aftership couriers
 */
class AftershipCouriers extends \XLite\Model\Repo\ARepo
{
    /**
     * Prepare name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param                            $value
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value): void
    {
        if ($value) {
            $queryBuilder
                ->andWhere('a.name LIKE :courierName')
                ->setParameter('courierName', '%' . $value . '%');
        }
    }

    /**
     * @param array $value
     *
     * @return array|null
     */
    public function findCouriers(array $value): ?array
    {
        return $this->prepareFindCouriers($value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $value
     *
     * @return AQueryBuilder
     */
    public function prepareFindCouriers(array $value): AQueryBuilder
    {
        return $this->createQueryBuilder()
            ->andWhere("a.slug IN ('" . implode("','", $value) . "')");
    }
}
