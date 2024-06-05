<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XLite\Model\Repo\ARepo;
use XLite\View\FormField\Input\Text\DateRange;

class Log extends ARepo
{
    const P_DIRECTION  = 'direction';
    const P_STATUS     = 'status';
    const P_OPERATION  = 'operation';
    const P_DATE_RANGE = 'dateRange';

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param integer $value Condition data
     *
     * @return void
     */
    protected function prepareCndDirection(QueryBuilder $queryBuilder, $value)
    {
        $directions = [
            Directions::DIR_XC_TO_SKUVAULT,
            Directions::DIR_SKUVAULT_TO_XC,
        ];

        if (!empty($value) && in_array($value, $directions)) {
            $queryBuilder->andWhere('l.direction = :direction')
                         ->setParameter('direction', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param integer $value Condition data
     *
     * @return void
     */
    protected function prepareCndStatus(QueryBuilder $queryBuilder, $value)
    {
        $statuses = [
            SyncStatuses::STATUS_SUCCESS,
            SyncStatuses::STATUS_ERROR,
        ];

        if (!empty($value) && in_array($value, $statuses)) {
            $queryBuilder->andWhere('l.status = :status')
                         ->setParameter('status', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array $value Condition data
     *
     * @return void
     */
    protected function prepareCndOperation(QueryBuilder $queryBuilder, $value)
    {
        foreach ($value as $k => $v) {
            if (empty($v)) {
                unset($value[$k]);
            }
        }

        if (!empty($value)) {
            if (is_array($value)) {
                $queryBuilder->andWhere('l.operation IN (\'' . implode("','", $value) . '\')');
            }

            if (is_string($value)) {
                $queryBuilder->andWhere('l.operation IN (\'' . $value . '\')');
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param integer $value Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            [$start, $end] = is_array($value) ? $value : DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('l.date >= :start')
                             ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('l.date <= :end')
                             ->setParameter('end', $end);
            }
        }
    }
}
