<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model\Repo;

/**
 * Returns repository
 *
 */
class OrderReturn extends \XLite\Model\Repo\ARepo
{
    /*
     * Allowed search parameters
     */
    public const SEARCH_ORDER_BY    = 'orderBy';
    public const SEARCH_LIMIT       = 'limit';
    public const SEARCH_EMAIL       = 'customerEmail';
    public const SEARCH_ORDER       = 'order';
    public const SEARCH_ONLY_ISSUED = 'onlyIssued';

    /**
     * Current condition
     *
     * @var \XLite\Core\CommonCell
     */
    protected $currentSearchCnd = null;

    /**
     * Find returns by ID and delete them
     *
     * @param array   $data  Array of <id => array(properties)> elements
     * @param boolean $flush Flag OPTIONAL
     *
     * @return void
     */
    public function deleteReturnsById(array $data, $flush = self::FLUSH_BY_DEFAULT)
    {
        foreach ($data as $id => $tmp) {
            $return = $this->getById($id);
            $order = $return->getOrder();
            $this->performDelete($return);
            $order->setOrderReturn(null);
        }

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndCustomerEmail(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $queryBuilder->linkInner($this->getMainAlias($queryBuilder) . '.order', 'ord');
            $queryBuilder->innerJoin('ord.profile', 'p');
            $queryBuilder->andWhere('p.login LIKE :emailLike')
                ->setParameter('emailLike', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrder(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder
                ->linkInner("{$this->getMainAlias($queryBuilder)}.order", 'ord')
                ->andWhere('ord.orderNumber = :orderNumber')
                ->setParameter('orderNumber', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOnlyIssued(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.status = :status')
                ->setParameter('status', \QSL\Returns\Model\OrderReturn::STATUS_ISSUED);
        }
    }
}
