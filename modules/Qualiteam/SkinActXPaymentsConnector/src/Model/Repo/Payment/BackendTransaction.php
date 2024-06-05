<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model\Repo\Payment;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * Payment backend transaction repository
 *
 * @Extender\Mixin
 */
class BackendTransaction extends \XLite\Model\Repo\Payment\BackendTransaction
{
    /**
     * Allowable search params
     */
    const SEARCH_ORDER_ID       = 'orderId';
    const SEARCH_TRANSACTION_ID = 'transactionId';

    /**
     * Default model alias
     *
     * @var string
     */
    protected $defaultAlias = 'bt';

    /**
     * Create new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        $qb = parent::createQueryBuilder($alias, $indexBy)
            ->innerJoin('bt.payment_transaction', 'pt')
            ->innerJoin('pt.order', 'o');

        return $qb;
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndOrderId(QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('o.order_id = :order_id')
                ->setParameter('order_id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndTransactionId(QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('pt.transaction_id = :transaction_id')
                ->setParameter('transaction_id', $value);
        }
    }
}
