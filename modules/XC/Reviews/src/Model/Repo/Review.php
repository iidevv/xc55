<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\Repo;

class Review extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    /**
     * Additional search modes
     */
    public const SEARCH_MODE_AVG = 'searchAvg';

    public const SEARCH_TYPE_REVIEWS_ONLY = 100;
    public const SEARCH_TYPE_RATINGS_ONLY = 200;
    public const SEARCH_ADDITION_DATE = 'additionDate';
    public const SEARCH_STATUS = 'status';
    public const SEARCH_REVIEWER_NAME = 'reviewerName';
    public const SEARCH_EMAIL = 'email';
    public const SEARCH_REVIEW = 'review';
    public const SEARCH_PRODUCT = 'product';
    public const SEARCH_KEYWORDS = 'keywords';
    public const SEARCH_RATING = 'rating';
    public const SEARCH_ZONE = 'zone';
    public const SEARCH_DATE_RANGE = 'dateRange';
    public const SEARCH_TYPE = 'type';
    public const SEARCH_NEW = 'isNew';
    public const SEARCH_ZONE_CUSTOMER = 'customer';

    /**
     * Get search modes handlers
     *
     * @return array
     */
    protected function getSearchModes()
    {
        return array_merge(
            parent::getSearchModes(),
            [
                static::SEARCH_MODE_AVG => 'searchAvg',
            ]
        );
    }

    /**
     * Search sum routine.
     *
     * @return integer
     */
    public function searchAvg()
    {
        $result = null;

        $queryBuilder = $this->searchState['queryBuilder'];

        if ($queryBuilder) {
            $result = $queryBuilder
                ->select('AVG(r.rating)')
                ->getSingleScalarResult();
        }

        return $result;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndAdditionDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {

        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : intval($value[0]);
            $end = empty($value[1]) ? null : intval($value[1]);

            if ($start == $end) {
                return;
            }

            if ($start) {
                $queryBuilder->andWhere('r.additionDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('r.additionDate <= :end')
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
        if ($value && is_array($value)) {
            [$start, $end] = $value;

            if ($start) {
                $queryBuilder->andWhere('r.additionDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('r.additionDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value == \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY) {
            $queryBuilder->andWhere('r.review != :type')
                ->setParameter('type', '');
        } elseif ($value == \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_RATINGS_ONLY) {
            $queryBuilder->andWhere('r.review = :type')
                ->setParameter('type', '');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndZone(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $values = array_values($value);

        if (
            isset($values[0])
            && $values[0] == self::SEARCH_ZONE_CUSTOMER
            && \XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews == true
        ) {
            $statusCondition = 'r.status = ' . \XC\Reviews\Model\Review::STATUS_APPROVED;

            if ($values[1] instanceof \XLite\Model\Profile) {
                $queryBuilder->linkLeft('r.profile', 'u');

                $queryBuilder->andWhere($statusCondition . ' OR u.profile_id = :profileId')
                    ->setParameter('profileId', $values[1]->getProfileId());
            } else {
                $queryBuilder->andWhere($statusCondition);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndStatus(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value) && $value != '%') {
            $queryBuilder->andWhere('r.status = :status')
                ->setParameter('status', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndRating(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value) && $value != '%') {
            if (!is_array($value)) {
                $value = [$value];
            }

            $value = array_filter($value);

            if (count($value) == 1) {
                $queryBuilder->andWhere('r.rating = :rating')
                    ->setParameter('rating', $value[0]);
            } elseif (1 < count($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('r.rating', $value));
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndReviewerName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder->linkLeft("{$alias}.profile", 'profile')
                ->andWhere("{$alias}.reviewerName LIKE :reviewerName OR profile.login LIKE :reviewerName")
                ->setParameter('reviewerName', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndKeywords(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->linkInner('r.product', 'p')
                ->linkLeft('r.profile', 'profile')
                ->innerJoin('p.translations', 'translations');

            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                'r.reviewerName LIKE :keywords',
                'profile.login LIKE :keywords',
                'p.sku LIKE :keywords',
                'translations.name LIKE :keywords'
            ))
                ->setParameter('keywords', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEmail(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->linkLeft('r.profile', 'profile')
                ->andWhere('profile.login LIKE :email')
                ->setParameter('email', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndReview(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('r.review LIKE :review')
                ->setParameter('review', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $productId = $value instanceof \XLite\Model\Product
            ? $value->getProductId()
            : (int) $value;

        if ($productId) {
            $queryBuilder->linkInner('r.product', 'p');
            $queryBuilder->andWhere('p.product_id = :productId')
                ->setParameter('productId', $productId);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProfile(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value instanceof \XLite\Model\Profile) {
            $queryBuilder->linkInner('r.profile', 'u');

            $queryBuilder->andWhere('u.profile_id = :profileId')
                ->setParameter('profileId', $value->getProfileId());
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndIsNew(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('r.isNew = :newStatus')
            ->setParameter('newStatus', (bool)$value ? 1 : 0);
    }

    // {{{ Meta

    public function setUseForMeta($productId, $reviewId)
    {
        $review = $this->findOneBy([
            'id' => $reviewId,
            'product' => $productId,
        ]);

        if ($review) {
            $this->getQueryBuilder()
                ->update($this->_entityName, 'r')
                ->set('r.useForMeta', 0)
                ->where('r.product = :product')
                ->setParameter('product', $productId)
                ->execute();

            $review->setUseForMeta(true);
            $review->update();
        }
    }

    /**
     * Returns review for meta description
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \XC\Reviews\Model\Review
     */
    public function findOneForMeta($product)
    {
        $queryBuilder = $this->defineFindOneForMeta($product);

        return $queryBuilder->getSingleResult();
    }

    /**
     * Returns query builder
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneForMeta($product)
    {
        $queryBuilder = $this->createQueryBuilder();

        $this->prepareCndProduct($queryBuilder, $product);
        $this->prepareCndStatus($queryBuilder, \XC\Reviews\Model\Review::STATUS_APPROVED, false);

        $queryBuilder->orderBy('r.useForMeta', 'DESC')
            ->addOrderBy('r.rating', 'DESC');

        return $queryBuilder;
    }

    // }}}

    public function getVotesCount($product, $status)
    {
        $queryBuilder = $this->createQueryBuilder();

        $this->prepareCndProduct($queryBuilder, $product);
        if ($status) {
            $this->prepareCndStatus($queryBuilder, $status, false);
        }

        $queryBuilder->select('r.rating rating')->addSelect('COUNT(r.id) votes');
        $queryBuilder->groupBy('r.rating');

        $votes = $queryBuilder->getArrayResult();
        $result = [];

        foreach ($votes as $rating) {
            $result[$rating['rating']] = (int) $rating['votes'];
        }

        return $result;
    }
}
