<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * Order repository
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Repo\Order
{
    const SEARCH_SUBSCRIPTION = 'subscription';

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder                                  $queryBuilder Query builder to prepare
     * @param \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubscription(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->linkInner('o.items')
                ->andWhere('items.subscription = :subscription')
                ->setParameter('subscription', $value);
        }
    }
}
