<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Order model repository extension
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Repo\Order
{
    /**
     * Return count by status
     *
     * @param string $statusType Status type
     *
     * @return array
     */
    public function countByStatus($statusType)
    {
        $statusType .= 'Status';

        $result = [];
        $data = $this->defineCountByStatusQuery($statusType)->getResult();

        foreach ($data as $v) {
            $result[$v['id']] = $v[1];
        }

        return $result;
    }

    /**
     * @param $statusType
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountByStatusQuery($statusType)
    {
        return $this->createPureQueryBuilder('o')
            ->select('COUNT(o.order_id)')
            ->innerJoin('o.' . $statusType, 's')
            ->addSelect('s.id')
            ->andWhere('o.orderNumber IS NOT NULL')
            ->groupBy('o.' . $statusType);
    }
}
