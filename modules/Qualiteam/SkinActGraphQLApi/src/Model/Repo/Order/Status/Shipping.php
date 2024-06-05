<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Repo\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Repo\Order\Status\Shipping
{
    public function getShippingBarStatuses()
    {
        return $this->createQueryBuilder('s')
            ->select('translations.name')
            ->andWhere('s.showInStatusesBar = :showInStatusesBar_true')
            ->setParameter('showInStatusesBar_true', true)
            ->orderBy('s.newPosition', 'ASC')
            ->getArrayResult();
    }
}
