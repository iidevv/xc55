<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model\Repo;

/**
 * Abstract record repository
 */
abstract class AbsRecord extends \XLite\Model\Repo\ARepo
{
    public const SEARCH_SUBSTRING  = 'substring';
    public const SEARCH_DATE_RANGE = 'dateRange';
    public const SEARCH_STATE      = 'state';
    public const SEARCH_ORDERBY    = 'orderBy';
    public const SEARCH_INCLUDING  = 'including';
    public const SEARCH_LIMIT      = 'limit';

    /**
     * Update records as changed - by product
     *
     * @param \XLite\Model\Product $product Product
     */
    public function updateAsChanged(\XLite\Model\Product $product)
    {
        /** @var \QSL\BackInStock\Model\ARecord $record */
        foreach ($this->findRecordsByState($product) as $record) {
            if ($record->checkWaiting()) {
                $this->defineRecordUpdateQuery($record, \QSL\BackInStock\Model\Record::STATE_READY)->execute();
            }
        }

        /** @var \QSL\BackInStock\Model\ARecord $record */
        foreach ($this->findRecordsByState($product, \QSL\BackInStock\Model\Record::STATE_READY) as $record) {
            if (!$record->checkWaiting()) {
                $this->defineRecordUpdateQuery($record, \QSL\BackInStock\Model\Record::STATE_STANDBY)->execute();
            }
        }
    }

    /**
     * Send notifications
     *
     * @return integer[]
     */
    abstract public function sendNotifications();

    /**
     * Check - record with specified conditions - exists or not
     *
     * @param \XLite\Model\Product $product Product
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     * @param string               $hash    Hash OPTIONAL
     *
     * @return boolean
     */
    public function hasRecordBySet(\XLite\Model\Product $product, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return (bool)$this->defineHasRecordBySetQuery($product, $profile, $hash)->getSingleScalarResult();
    }

    /**
     * Get record with specified conditions
     *
     * @param \XLite\Model\Product $product Product
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     * @param string               $hash    Hash OPTIONAL
     *
     * @return \QSL\BackInStock\Model\Record
     */
    public function getRecordBySet(\XLite\Model\Product $product, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->defineGetRecordBySetQuery($product, $profile, $hash)->getSingleResult();
    }

    /**
     * Get waited record with specified conditions
     *
     * @param \XLite\Model\Product $product Product
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     * @param string               $hash    Hash OPTIONAL
     *
     * @return \QSL\BackInStock\Model\Record
     */
    public function getWaitedRecordBySet(\XLite\Model\Product $product, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->defineGetWaitedRecordBySetQuery($product, $profile, $hash)->getSingleResult();
    }

    /**
     * Count waiting records by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function countWaiting(\XLite\Model\Product $product)
    {
        return $this->defineCountWaitingQuery($product)->count();
    }

    /**
     * Find stand-by records by product
     *
     * @param \XLite\Model\Product $product Product
     * @param integer
     *
     * @return \QSL\BackInStock\Model\Record[]
     */
    public function findRecordsByState(\XLite\Model\Product $product, $state = \QSL\BackInStock\Model\Record::STATE_STANDBY)
    {
        return $this->defineRecordsByStateQuery($product, $state)->getResult();
    }

    /**
     * Find stand-by records
     *
     * @return \QSL\BackInStock\Model\ARecord[]
     */
    public function findStandby()
    {
        return $this->defineFindStandbyQuery()->getResult();
    }

    /**
     * Find all unset notifications
     *
     * @return \QSL\BackInStock\Model\Record[]
     */
    public function findUnsentNotifications()
    {
        return $this->defineFindUnsentNotificationsQuery()->getResult();
    }

    /**
     * Define query builder for 'hasRecordBySet' method
     *
     * @param \XLite\Model\Product $product Product
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     * @param string               $hash    Hash OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineHasRecordBySetQuery(\XLite\Model\Product $product, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.product = :product AND (r.profile = :profile OR r.hash = :hash)')
            ->setParameter('product', $product)
            ->setParameter('profile', $profile)
            ->setParameter('hash', $hash);
    }

    /**
     * Define query builder for 'getRecordBySet' method
     *
     * @param \XLite\Model\Product $product Product
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     * @param string               $hash    Hash OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetRecordBySetQuery(\XLite\Model\Product $product, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND (r.profile = :profile OR r.hash = :hash)')
            ->setParameter('product', $product)
            ->setParameter('profile', $profile)
            ->setParameter('hash', $hash);
    }

    /**
     * Define query builder for 'getWaitedRecordBySet' method
     *
     * @param \XLite\Model\Product $product Product
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     * @param string               $hash    Hash OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetWaitedRecordBySetQuery(\XLite\Model\Product $product, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND (r.profile = :profile OR r.hash = :hash) AND r.state != :sent')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->setParameter('profile', $profile)
            ->setParameter('hash', $hash);
    }

    /**
     * Define query for 'countWaiting' method
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountWaitingQuery(\XLite\Model\Product $product)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND r.state != :sent')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product);
    }

    /**
     * Define query for 'findRecordsByState' method
     *
     * @param \XLite\Model\Product $product Product
     * @param integer
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRecordsByStateQuery(\XLite\Model\Product $product, int $state)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND r.state = :recordState')
            ->setParameter('product', $product)
            ->setParameter('recordState', $state);
    }

    /**
     * Define query for 'findStandby' method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindStandbyQuery()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.state = :standby')
            ->setParameter('standby', \QSL\BackInStock\Model\Record::STATE_STANDBY);
    }

    /**
     * Define query for 'findUnsentNotifications' method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindUnsentNotificationsQuery()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.state IN (:standby, :bounced)')
            ->setParameter('standby', \QSL\BackInStock\Model\Record::STATE_READY)
            ->setParameter('bounced', \QSL\BackInStock\Model\Record::STATE_BOUNCED);
    }

    // {{{ Logic

    /**
     * Check all waiting records
     *
     * @return integer
     */
    public function checkWaiting()
    {
        $updated = 0;
        foreach ($this->findStandby() as $record) {
            /** @var \QSL\BackInStock\Model\ARecord $record */
            if ($record->checkWaiting()) {
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Check records with 'sending' state.
     * If record has 'sending' state 1 hour or more - record's state will be change to 'bounced'
     */
    public function checkSendingRecords()
    {
        $this->defineCheckSendingRecordsQuery()->execute();
    }

    /**
     * Define query for 'checkSendingRecords' method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCheckSendingRecordsQuery()
    {
        return $this->getQueryBuilder()
            ->update($this->_entityName, 'r')
            ->set('r.state', \QSL\BackInStock\Model\Record::STATE_BOUNCED)
            ->set('r.startSendingDate', 'NULL')
            ->andWhere('r.state = :sending AND r.startSendingDate < :limit')
            ->setParameter('sending', \QSL\BackInStock\Model\Record::STATE_SENDING)
            ->setParameter('limit', \XLite\Core\Converter::time() - 3600);
    }

    /**
     * Define record update query
     *
     * @param \QSL\BackInStock\Model\ARecord $record Record
     * @param integer
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRecordUpdateQuery(\QSL\BackInStock\Model\ARecord $record, int $state)
    {
        return $this->getQueryBuilder()
            ->update($this->_entityName, 'r')
            ->set('r.backDate', ':date')
            ->set('r.state', ':state')
            ->where('r.id = :id')
            ->setParameter('date', \XLite\Core\Converter::time())
            ->setParameter('state', $state)
            ->setParameter('id', $record->getId());
    }

    // }}}

    // {{{ Search

    /**
     * @inheritdoc
     */
    public function getQueryBuilderForSearch()
    {
        $queryBuilder = parent::getQueryBuilderForSearch()->linkInner('r.product')
            ->linkLeft('product.translations', 'ptranslations')
            ->linkLeft('r.profile');
        if ($this->searchState['searchMode'] !== static::SEARCH_MODE_COUNT) {
            $queryBuilder->groupBy('r.id');
        }

        return $queryBuilder;
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     */
    protected function prepareCndSubstring(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value) && !empty($this->searchState['currentSearchCnd']->{static::SEARCH_INCLUDING})) {
            $where = [];
            $includings = $this->searchState['currentSearchCnd']->{static::SEARCH_INCLUDING} ?: ['product', 'email'];
            foreach ($includings as $include => $flag) {
                if ($flag) {
                    $method = 'addSubstringConditionBy' . ucfirst($include);
                    if (method_exists($this, $method)) {
                        $this->$method($queryBuilder, $where, $value);
                    }
                }
            }

            if ($where) {
                $queryBuilder->andWhere('((' . implode(') OR (', $where) . '))');
            }
        }
    }

    /**
     * Add substring condition (by product name)
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param array                                   &$where       Where list
     * @param string                                  $value        Value
     */
    protected function addSubstringConditionByProduct(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, array &$where, $value)
    {
        $queryBuilder->andWhere('ptranslations.code = :lng')
            ->setParameter('lng', \XLite\Core\Session::getInstance()->getLanguage()->getCode())
            ->setParameter('name', '%' . $value . '%');
        $where[] = 'ptranslations.name LIKE :name';
    }

    /**
     * Add substring condition (by email name)
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param array                                   &$where       Where list
     * @param string                                  $value        Value
     */
    protected function addSubstringConditionByEmail(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, array &$where, $value)
    {
        $queryBuilder->setParameter('email', '%' . $value . '%');
        $where[] = 'r.email LIKE :email';
        $where[] = 'profile.login LIKE :email';
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     */
    protected function prepareCndDateRange(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            [$start, $end] = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
            if ($start) {
                $queryBuilder->andWhere('r.date >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('r.date <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     */
    protected function prepareCndState(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('r.state = :state')
                ->setParameter('state', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     */
    protected function prepareCndIncluding(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
    }

    // }}}
}
