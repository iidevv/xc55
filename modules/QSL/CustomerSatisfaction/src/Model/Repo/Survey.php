<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model\Repo;

use Doctrine\ORM\EntityManager;

/**
 * Order repository
 */
class Survey extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    public const SEARCH_ORDERBY    = 'orderBy';
    public const SEARCH_EMAIL_DATE = 'additionDate';
    public const SEARCH_STATUS     = 'status';
    public const SEARCH_KEYWORDS   = 'keywords';
    public const SEARCH_RATING     = 'rating';
    public const SEARCH_LIMIT      = 'limit';
    public const SEARCH_DATE_RANGE = 'dateRange';
    public const SEARCH_ORDER_ID   = 'orderId';

    /**
     * Search count only routine.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return void
     */
    public function createSurvey(\XLite\Model\Order $order)
    {
        if ($order->getProfile()) {
            if (!$order->getSurvey()) {
                $survey = new \QSL\CustomerSatisfaction\Model\Survey();
                $survey->setOrder($order);
                $survey->setInitDate(\XLite\Core\Converter::time());
                $survey->setCustomer($order->getProfile());
                $survey->setHashKey(md5(\XLite\Core\Converter::time() . $order->getProfile()->getLogin() . $order->getOrderId()));
                \XLite\Core\Database::getEM()->persist($survey);
                $order->setSurvey($survey);
            } else {
                $survey = $order->getSurvey();
            }

            if (!$survey->getEmailDate()) {
                $survey->setEmailDate(\XLite\Core\Converter::time());

                $data = [
                    'surveyId'     => $survey->getId(),
                    'customerName' => $order->getProfile()->getName(),
                    'order'        => $order,
                    'surveyKey'    => $survey->getHashKey()
                ];

                \XLite\Core\Mailer::getInstance()->sendCustomerSatisfactionNotification(
                    $data
                );
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLimit(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        $queryBuilder->setFrameResults($value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndRating(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value) && !empty($value[0])) {
            $queryBuilder->andWhere('s.rating IN (:rating)')
                ->setParameter('rating', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndStatus(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value) && $value == 'unclosed') {
            $value = 'C';
            $queryBuilder->andWhere('s.status <> :status')
                ->setParameter('status', $value);
        } elseif (isset($value) && $value != 'all' && $value != 'unclosed' && $value != '') {
            $queryBuilder->andWhere('s.status = :status')
                ->setParameter('status', $value);
        }
        $queryBuilder->andWhere('s.status <> :hiddenStatus')
            ->setParameter('hiddenStatus', \QSL\CustomerSatisfaction\Model\Survey::STATUS_HIDDEN);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            [$start, $end] = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('s.feedbackDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('s.feedbackDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEmailDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {

        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : intval($value[0]);
            $end = empty($value[1]) ? null : intval($value[1]);

            if ($start == $end) {
                return;
            }

            if ($start) {
                $queryBuilder->andWhere('s.feedbackDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('s.feedbackDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndKeywords(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            if (is_numeric($value)) {
                $queryBuilder->linkInner('s.order', 'o')
                    ->andWhere('o.orderNumber = :orderNumber')
                    ->setParameter('orderNumber', $value);
            } else {
                $queryBuilder
                    ->linkInner('s.customer', 'p')
                    ->linkInner('p.addresses', 'addresses');

                $cnd = new \Doctrine\ORM\Query\Expr\Orx();

                $this->prepareField($queryBuilder, 'firstname');
                $this->prepareField($queryBuilder, 'lastname');

                foreach ($this->getKeywordSearchFields() as $field) {
                    $cnd->add($field . ' LIKE :pattern');
                }

                $queryBuilder
                    ->andWhere($cnd)
                    ->setParameter('pattern', '%' . $value . '%');
            }
        }
    }

    /**
     * @return string
     */
    public function getDefaultOrderBy()
    {
        return $this->defaultOrderBy;
    }

    /**
     * @param string $defaultOrderBy
     */
    public function setDefaultOrderBy($defaultOrderBy)
    {
        $this->defaultOrderBy = $defaultOrderBy;
    }

    /**
     * @return array
     */
    public function getAlternativeIdentifier()
    {
        return $this->alternativeIdentifier;
    }

    /**
     * @param array $alternativeIdentifier
     */
    public function setAlternativeIdentifier($alternativeIdentifier)
    {
        $this->alternativeIdentifier = $alternativeIdentifier;
    }

    /**
     * @return boolean
     */
    public function isFlushAfterLoading()
    {
        return $this->flushAfterLoading;
    }

    /**
     * @param boolean $flushAfterLoading
     */
    public function setFlushAfterLoading($flushAfterLoading)
    {
        $this->flushAfterLoading = $flushAfterLoading;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getQueryBuilderClass()
    {
        return $this->queryBuilderClass;
    }

    /**
     * @param string $queryBuilderClass
     */
    public function setQueryBuilderClass($queryBuilderClass)
    {
        $this->queryBuilderClass = $queryBuilderClass;
    }

    /**
     * @return array
     */
    public function getSearchState()
    {
        return $this->searchState;
    }

    /**
     * @param array $searchState
     */
    public function setSearchState($searchState)
    {
        $this->searchState = $searchState;
    }

    /**
     * @return boolean
     */
    public function isHasFilter()
    {
        return $this->hasFilter;
    }

    /**
     * @param boolean $hasFilter
     */
    public function setHasFilter($hasFilter)
    {
        $this->hasFilter = $hasFilter;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->_em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->_em = $em;
    }

    /**
     * @return Mapping\ClassMetadata
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * @param Mapping\ClassMetadata $class
     */
    public function setClass($class)
    {
        $this->_class = $class;
    }

    // }}}

    /**
     * Prepare field search query
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string                     $fieldName    Field name
     *
     * @return void
     */
    protected function prepareField(\Doctrine\ORM\QueryBuilder $queryBuilder, $fieldName)
    {
        $queryBuilder->leftJoin(
            'addresses.addressFields',
            'field_value_' . $fieldName
        )->leftJoin(
            'field_value_' . $fieldName . '.addressField',
            'field_' . $fieldName,
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'field_' . $fieldName . '.serviceName = :' . $fieldName
        )->setParameter($fieldName, $fieldName);
    }

    /**
     * List of fields to use in search by substring
     *
     * @return array
     */
    protected function getKeywordSearchFields()
    {
        return [
            'CONCAT(CONCAT(field_value_firstname.value, \' \'), field_value_lastname.value)',
            'CONCAT(CONCAT(field_value_lastname.value, \' \'), field_value_firstname.value)',
            'p.login'
            //'t.name'
        ];
    }
}
