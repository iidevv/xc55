<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 *
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{

    public const P_ORDER_IN_PROGRESS = 'inProgress';

    protected function prepareCndInProgress(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('o.orderCreatedNotificationSent = 0');
            $queryBuilder->andWhere('o.manuallyCreated = 1');
        }
    }

}
