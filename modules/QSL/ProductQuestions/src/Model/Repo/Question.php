<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Model\Repo;

/**
 * Repository class for the Questions model.
 */
class Question extends \XLite\Model\Repo\ARepo
{
    // {{{ Search
    public const SEARCH_DATE           = 'date';
    public const SEARCH_DATE_RANGE     = 'dateRange';
    public const SEARCH_PUBLISHED      = 'published';
    public const SEARCH_PRIVATE        = 'private';
    public const SEARCH_PRODUCT        = 'product';
    public const SEARCH_PRODUCT_ID     = 'productId';
    public const SEARCH_PROFILE        = 'profile';
    public const SEARCH_PROFILE_ID     = 'profileId';
    public const SEARCH_IDS            = 'ids';
    public const SEARCH_NOT_PROFILE    = 'notProfile';
    public const SEARCH_NOT_PROFILE_ID = 'notProfileId';
    public const SEARCH_NOT_IDS        = 'notIds';

    // Order By fields
    public const SEARCH_ORDERBY_PUBLISHED = 'q.published';
    public const SEARCH_ORDERBY_DATE      = 'q.date';

    protected $cachedQuestionCount = [];

    /**
     * Search for unanswered questions.
     *
     * @param boolean $countOnly Whether to count the number of matching records, or return the whole set of them
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchUnansweredQuestions($countOnly = false)
    {
        return $this->search($this->defineSearchUnansweredQuestionsCondition(), $countOnly);
    }

    /**
     * Get the number of product questions visible to a customer.
     *
     * @param \XLite\Model\Product|integer $product Product that we count questions for
     * @param \XLite\Model\Profile         $profile Profile that we count questions for
     * @param array                        $ids     Identifiers of questions asked the current guest visitor (if any)
     *
     * @return mixed
     */
    public function countQuestionsVisibleToUser($product, \XLite\Model\Profile $profile = null, array $ids)
    {
        $productId = ($product instanceof \XLite\Model\Product)
            ? $product->getProductId()
            : (int) $product;

        $profileId = $profile ? $profile->getProfileId() : 0;

        if (!isset($this->cachedQuestionCount[$productId][$profileId])) {
            $this->cachedQuestionCount[$productId][$profileId] = $this->findUserProductQuestions(
                $productId,
                $profile,
                $ids,
                true
            );
            $this->cachedQuestionCount[$productId][$profileId] += $this->findOthersProductQuestions(
                $productId,
                $profile,
                $ids,
                true
            );
        }

        return $this->cachedQuestionCount[$productId][$profileId];
    }

    /**
     * Search for questions the customer asked about the product.
     *
     * @param \XLite\Model\Product|integer $product   Product that we search questions for
     * @param \XLite\Model\Profile|integer $profile   Profile that we search questions for OPTIONAL
     * @param array                        $ids       Limit the query to questions having these identifiers only OPTIONAL
     * @param boolean                      $countOnly Return items list or only its size OPTIONAL
     * @param integer                      $max       Maximum number of items to return OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function findUserProductQuestions(
        $product,
        $profile = null,
        array $ids = [],
        $countOnly = false,
        $max = 0
    ) {
        $cnd = $this->getBaseProductQuestionsSearchConditions($product, $max);

        if ($profile instanceof \XLite\Model\Profile) {
            $cnd->{static::SEARCH_PROFILE} = $profile;
        } elseif ($profile) {
            $cnd->{static::SEARCH_PROFILE_ID} = (int) $profile;
        } else {
            $cnd->{static::SEARCH_IDS} = $ids;
        }

        return $this->search($cnd, $countOnly);
    }

    /**
     * Search for questions other customers asked about the product.
     *
     * @param \XLite\Model\Product|integer $product   Product that we search questions for
     * @param \XLite\Model\Profile|integer $profile   Profile that is viewing the list OPTIONAL
     * @param array                        $ids       Exclude questions with these identifiers from the query OPTIONAL
     * @param boolean                      $countOnly Return items list or only its size OPTIONAL
     * @param integer                      $max       Maximum number of items to return OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function findOthersProductQuestions(
        $product,
        $profile = null,
        array $ids = [],
        $countOnly = false,
        $max = 0
    ) {
        $cnd = $this->getBaseProductQuestionsSearchConditions($product, $max);

        $cnd->{static::SEARCH_PUBLISHED} = true;
        $cnd->{static::SEARCH_PRIVATE} = false;

        if ($profile instanceof \XLite\Model\Profile) {
            $cnd->{static::SEARCH_NOT_PROFILE} = $profile;
        } elseif ($profile) {
            $cnd->{static::SEARCH_NOT_PROFILE_ID} = (int) $profile;
        } else {
            $cnd->{static::SEARCH_NOT_IDS} = $ids;
        }

        return $this->search($cnd, $countOnly);
    }

    /**
     * Get the base search conditions for retrieving product questions.
     *
     * @param \XLite\Model\Product|integer $product   Product that we search questions for
     * @param integer                      $max       Maximum number of questions to return
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getBaseProductQuestionsSearchConditions($product, $max)
    {
        $cnd = new \XLite\Core\CommonCell();

        if ($product instanceof \XLite\Model\Product) {
            $cnd->{static::SEARCH_PRODUCT} = $product;
        } else {
            $cnd->{static::SEARCH_PRODUCT_ID} = (int) $product;
        }

        $cnd->{static::P_ORDER_BY} = [
            [
            static::SEARCH_ORDERBY_PUBLISHED,
            \XLite\View\ItemsList\AItemsList::SORT_ORDER_DESC,
            ],
            [
            static::SEARCH_ORDERBY_DATE,
            \XLite\View\ItemsList\AItemsList::SORT_ORDER_DESC,
            ]
        ];

        if ($max) {
            $cnd->{static::P_LIMIT} = [0, $max];
        }

        return $cnd;
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
    protected function prepareCndDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : (int) $value[0];
            $end = empty($value[1]) ? null : (int) $value[1];

            if ($start === $end) {
                return;
            }

            if ($start) {
                $queryBuilder->andWhere('q.date >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('q.date <= :end')
                    ->setParameter('end', $end);
            }
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
    protected function prepareCndDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            [$start, $end] = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
            if ($start) {
                $queryBuilder->andWhere('q.date >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('q.date <= :end')
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
    protected function prepareCndPublished(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value)) {
            $queryBuilder->andWhere('q.published = :status')
                ->setParameter('status', $value);
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
    protected function prepareCndPrivate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value)) {
            $queryBuilder->andWhere('q.private = :private')
                ->setParameter('private', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Product       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, \XLite\Model\Product $value)
    {
        $this->prepareCndProductId($queryBuilder, $value->getProductId());
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Product ID
     *
     * @return void
     */
    protected function prepareCndProductId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('q.product', 'p')
          ->andWhere('p.product_id = :productId')
          ->setParameter('productId', (int) $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProfile(\Doctrine\ORM\QueryBuilder $queryBuilder, \XLite\Model\Profile $value)
    {
        $this->prepareCndProfileId($queryBuilder, $value->getProfileId());
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('q.profile', 'u')
            ->andWhere('u.profile_id = :profileId')
            ->setParameter('profileId', (int) $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndIds(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (empty($value)) {
            // We have to restrict items in the result to a set of IDs, otherwise all questions will be retrieved
            $value = [0];
        }
        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('q.id', $value)
        );
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndNotProfile(\Doctrine\ORM\QueryBuilder $queryBuilder, \XLite\Model\Profile $value)
    {
        $this->prepareCndNotProfileId($queryBuilder, $value->getProfileId());
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndNotProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->leftJoin('q.profile', 'u')
            ->andWhere('(u.profile_id IS NULL) OR (u.profile_id <> :profileId)')
            ->setParameter('profileId', (int) $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndNotIds(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!empty($value)) {
            // We just ignore the condition if the list of prohibited IDs is empty
            $queryBuilder->andWhere(
                $queryBuilder->expr()->notIn('q.id', $value)
            );
        }
    }

    /**
     * Defines the conditions required to retrieve unanswered questions.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function defineSearchUnansweredQuestionsCondition()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::SEARCH_PUBLISHED} = false;
        $cnd->{static::P_ORDER_BY} = [
            static::SEARCH_ORDERBY_DATE,
            \XLite\View\ItemsList\AItemsList::SORT_ORDER_DESC,
        ];

        return $cnd;
    }
}
