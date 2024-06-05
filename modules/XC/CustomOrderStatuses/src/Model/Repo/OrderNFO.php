<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Order model repository extension
 *
 * @Extender\Mixin
 * @Extender\After ("XC\CustomOrderStatuses")
 * @Extender\Depend ("XC\NotFinishedOrders")
 */
abstract class OrderNFO extends \XLite\Model\Repo\Order
{
    /**
     * @param $statusType
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountByStatusQuery($statusType)
    {
        $qb = parent::defineCountByStatusQuery($statusType);

        return strpos($statusType, 'shipping') === 0
            ? $this->addNotFinishedCnd($qb)
            : $qb;
    }
}
