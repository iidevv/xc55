<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    public const SEARCH_BACK_IN_STOCK = 'backInStock';
    public const SEARCH_PRICE_DROP    = 'priceDrop';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndBackInStock(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $dql = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                ->createQueryBuilder('r')
                ->select('product.product_id')
                ->linkInner('r.product')
                ->getDQL();
            $queryBuilder->andWhere($queryBuilder->expr()->in('p.product_id', $dql));
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndPriceDrop(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $dql = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->createQueryBuilder('r')
                ->select('product.product_id')
                ->linkInner('r.product')
                ->getDQL();
            $queryBuilder->andWhere($queryBuilder->expr()->in('p.product_id', $dql));
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSortOrderValue($value)
    {
        $result = parent::getSortOrderValue($value);
        if (empty($this->searchState['back_in_stock_sort_assigned'])) {
            if ($result[0] === 'records_waiting_count') {
                $dql = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                    ->createQueryBuilder('r2')
                    ->select('COUNT(r2.id)')
                    ->linkInner('r2.product', 'product2')
                    ->andWhere('product2.product_id = p.product_id AND r2.state IN (:b2s_standby, :b2s_bounced, :b2s_ready)')
                    ->getDQL();
                $this->searchState['queryBuilder']->addSelect('(' . $dql . ') AS records_waiting_count')
                    ->setParameter('b2s_standby', \QSL\BackInStock\Model\ARecord::STATE_STANDBY)
                    ->setParameter('b2s_bounced', \QSL\BackInStock\Model\ARecord::STATE_BOUNCED)
                    ->setParameter('b2s_ready', \QSL\BackInStock\Model\ARecord::STATE_READY);
                $this->searchState['back_in_stock_sort_assigned'] = true;
            } elseif ($result[0] === 'records_sent_count') {
                $dql = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                    ->createQueryBuilder('r3')
                    ->select('COUNT(r3.id)')
                    ->linkInner('r3.product', 'product2')
                    ->andWHere('product2.product_id = p.product_id AND r3.state = :b2s_sent')
                    ->getDQL();
                $this->searchState['queryBuilder']->addSelect('(' . $dql . ') AS records_sent_count')
                    ->setParameter('b2s_sent', \QSL\BackInStock\Model\ARecord::STATE_SENT);
                $this->searchState['back_in_stock_sort_assigned'] = true;
            } elseif ($result[0] === 'records_price_waiting_count') {
                $dql = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                    ->createQueryBuilder('r4')
                    ->select('COUNT(r4.id)')
                    ->linkInner('r4.product', 'product2')
                    ->andWhere('product2.product_id = p.product_id AND r4.state IN (:b2s_standby, :b2s_bounced, :b2s_ready)')
                    ->getDQL();
                $this->searchState['queryBuilder']->addSelect('(' . $dql . ') AS records_price_waiting_count')
                    ->setParameter('b2s_standby', \QSL\BackInStock\Model\ARecord::STATE_STANDBY)
                    ->setParameter('b2s_bounced', \QSL\BackInStock\Model\ARecord::STATE_BOUNCED)
                    ->setParameter('b2s_ready', \QSL\BackInStock\Model\ARecord::STATE_READY);
                $this->searchState['back_in_stock_sort_assigned'] = true;
            } elseif ($result[0] === 'records_price_sent_count') {
                $dql = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                    ->createQueryBuilder('r5')
                    ->select('COUNT(r5.id)')
                    ->linkInner('r5.product', 'product2')
                    ->andWHere('product2.product_id = p.product_id AND r5.state = :b2s_sent')
                    ->getDQL();
                $this->searchState['queryBuilder']->addSelect('(' . $dql . ') AS records_price_sent_count')
                    ->setParameter('b2s_sent', \QSL\BackInStock\Model\ARecord::STATE_SENT);
                $this->searchState['back_in_stock_sort_assigned'] = true;
            }
        }

        return $result;
    }
}
