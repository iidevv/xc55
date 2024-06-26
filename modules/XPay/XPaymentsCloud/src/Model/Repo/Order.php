<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Order repository
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    const SEARCH_XPAYMENTS_SUBSCRIPTION = 'xpaymentsSubscription';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder                                  $queryBuilder Query builder to prepare
     * @param \XPay\XPaymentsCloud\Model\Subscription\Subscription $value        Condition data
     *
     * @return void
     */
    protected function prepareCndXpaymentsSubscription(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->linkInner('o.items')
                ->andWhere('items.xpaymentsSubscription = :xpaymentsSubscription')
                ->setParameter('xpaymentsSubscription', $value);
        }
    }
}
