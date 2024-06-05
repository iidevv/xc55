<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomOrderStatuses\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    public const P_MOBILE_TAB = 'mobileTab';

    protected function prepareCndMobileTab(QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder
                ->linkLeft('o.paymentStatus', 'ordp')
                ->andWhere('ordp.mobile_tab = :mobileTab')
                ->setParameter('mobileTab', $value);
        }
    }
}