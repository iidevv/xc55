<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\Model\Repo;

class VolumeDiscount extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    public const P_MEMBERSHIP = 'membership';
    public const P_SUBTOTAL = 'subtotal';
    public const P_SUBTOTAL_ADV = 'subtotalAdv';
    public const P_MIN_VALUE = 'minValue';
    public const P_ZONES = 'zones';
    public const P_TYPE = 'type';
    public const P_DATE = 'date';
    public const P_ORDER_BY_VALUE = 'orderByValue';
    public const P_ORDER_BY_SUBTOTAL = 'orderBySubtotal';
    public const P_ORDER_BY_SUBTOTAL_AND_VALUE = 'orderBySubtotalAndValue';
    public const P_ORDER_BY_MEMBERSHIP = 'orderByMembership';

    /**
     * Find similar discounts
     *
     * @param \CDev\VolumeDiscounts\Model\VolumeDiscount $model Discount
     *
     * @return \CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    public function findSimilarDiscounts(\CDev\VolumeDiscounts\Model\VolumeDiscount $model)
    {
        return $this->defineFindSimilarDiscountsQuery($model)->getResult();
    }

    /**
     * Define query for 'findSimilarDiscounts' method
     *
     * @param \CDev\VolumeDiscounts\Model\VolumeDiscount $model Discount
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindSimilarDiscountsQuery(\CDev\VolumeDiscounts\Model\VolumeDiscount $model)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('v.subtotalRangeBegin = :subtotalRangeBegin')
            ->andWhere('v.dateRangeBegin = :dateRangeBegin')
            ->andWhere('v.dateRangeEnd = :dateRangeEnd')
            ->setParameter('subtotalRangeBegin', $model->getSubtotalRangeBegin())
            ->setParameter('dateRangeBegin', $model->getDateRangeBegin())
            ->setParameter('dateRangeEnd', $model->getDateRangeEnd());

        if ($model->getMembership()) {
            $qb->andWhere('v.membership = :membership')
                ->setParameter('membership', $model->getMembership());
        } else {
            $qb->andWhere('v.membership IS NULL');
        }

        if ($model->getId()) {
            $qb->andWhere('v.id <> :id')
                ->setParameter('id', $model->getId());
        }

        return $qb;
    }

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $membershipRelation = false;

        foreach ($this->searchState['currentSearchCnd'] as $key => $value) {
            if (in_array($key, [self::P_MEMBERSHIP, self::P_ORDER_BY_MEMBERSHIP], true)) {
                $membershipRelation = true;
            }
        }

        if ($membershipRelation) {
            $this->searchState['queryBuilder']->leftJoin('v.membership', 'membership');
        }

        parent::processConditions();
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value !== null) {
            $cnd = new \Doctrine\ORM\Query\Expr\Orx();
            $cnd->add('membership.membership_id = :membershipId');
            $cnd->add('v.membership IS NULL');

            $queryBuilder->andWhere($cnd)
                ->setParameter('membershipId', $value);
        } else {
            $queryBuilder->andWhere('v.membership IS NULL');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndSubtotal(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.subtotalRangeBegin <= :subtotal')
            ->setParameter('subtotal', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndSubtotalAdv(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.subtotalRangeBegin > :subtotal')
            ->setParameter('subtotal', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndMinValue(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.value > :value')
            ->setParameter('value', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.type = :type')
            ->setParameter('type', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndZones(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->linkLeft('v.zones', 'zone');

        if ($value !== null) {
            $zoneIds = array_map(static function ($zone) {
                return $zone->getZoneId();
            }, $value);

            $cnd = $queryBuilder->expr()->orX();
            $cnd->add($queryBuilder->expr()->in('zone.zone_id', ':zoneIds'));
            $cnd->add('zone.zone_id IS NULL');

            $queryBuilder->andWhere($cnd);
            $queryBuilder->setParameter('zoneIds', $zoneIds);
        } else {
            $queryBuilder->andWhere('zone.zone_id IS NULL');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $dateRangeEndCnd = $queryBuilder->expr()->orX();
        $dateRangeEndCnd->add('v.dateRangeEnd >= :date');
        $dateRangeEndCnd->add('v.dateRangeEnd = 0');

        $cnd = $queryBuilder->expr()->andX();
        $cnd->add('v.dateRangeBegin <= :date');
        $cnd->add($dateRangeEndCnd);

        $queryBuilder->andWhere($cnd);
        $queryBuilder->setParameter('date', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderByValue(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderBySubtotalAndValue(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderBySubtotal(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderByMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    // }}}

    // {{{ Find suitable discount methods

    /**
     * Get suitable discount with max value for specified subtotal
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     *
     * @return \CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    public function getSuitableMaxDiscount($cnd)
    {
        // Get suitable percent and absolute discounts ordered by value,
        // so max value discount will be the first element
        $percentDiscounts = $this->search($this->getSuitablePercentDiscountsCondition($cnd));
        $absoluteDiscounts = $this->search($this->getSuitableAbsoluteDiscountsCondition($cnd));

        if ($percentDiscounts && $absoluteDiscounts) {
            $maxDiscount = $percentDiscounts[0]->getValue() * $cnd->{self::P_SUBTOTAL} / 100 > $absoluteDiscounts[0]->getValue()
                ? $percentDiscounts[0]
                : $absoluteDiscounts[0];
        } elseif ($percentDiscounts) {
            $maxDiscount = $percentDiscounts[0];
        } elseif ($absoluteDiscounts) {
            $maxDiscount = $absoluteDiscounts[0];
        } else {
            $maxDiscount = null;
        }

        return $maxDiscount;
    }

    /**
     * Get next discount
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     *
     * @return \CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    public function getNextDiscount($cnd)
    {
        // Get suitable percent and absolute discounts ordered by subtotal asc and value desc,
        // so max value discount will be the first element
        $percentDiscounts = $this->search($this->getSuitablePercentDiscountsCondition($cnd, true));
        $absoluteDiscounts = $this->search($this->getSuitableAbsoluteDiscountsCondition($cnd, true));

        if ($percentDiscounts && $absoluteDiscounts) {
            if ($percentDiscounts[0]->getSubtotalRangeBegin() < $absoluteDiscounts[0]->getSubtotalRangeBegin()) {
                $maxDiscount = $percentDiscounts[0];
            } elseif ($percentDiscounts[0]->getSubtotalRangeBegin() > $absoluteDiscounts[0]->getSubtotalRangeBegin()) {
                $maxDiscount = $absoluteDiscounts[0];
            } else {
                $maxDiscount = $percentDiscounts[0]->getValue() * $cnd->{self::P_SUBTOTAL} / 100 > $absoluteDiscounts[0]->getValue()
                    ? $percentDiscounts[0]
                    : $absoluteDiscounts[0];
            }
        } elseif ($percentDiscounts) {
            $maxDiscount = $percentDiscounts[0];
        } elseif ($absoluteDiscounts) {
            $maxDiscount = $absoluteDiscounts[0];
        } else {
            $maxDiscount = null;
        }

        return $maxDiscount;
    }

    /**
     * getSuitableDiscountsCondition
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     * @param bool $isNext
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSuitableDiscountsCondition($cnd, $isNext = false)
    {
        $result = new \XLite\Core\CommonCell();

        if ($isNext) {
            $result->{self::P_SUBTOTAL_ADV} = $cnd->{self::P_SUBTOTAL};
            $result->{self::P_ORDER_BY_SUBTOTAL_AND_VALUE} = [['v.subtotalRangeBegin', 'ASC'], ['v.value', 'DESC']];
        } else {
            $result->{self::P_SUBTOTAL} = $cnd->{self::P_SUBTOTAL};
            $result->{self::P_ORDER_BY_VALUE} = ['v.value', 'DESC'];
        }

        $membership = $cnd->{self::P_MEMBERSHIP};
        $result->{self::P_MEMBERSHIP} = $membership ? $membership->getMembershipId() : null;

        $result->{self::P_ZONES} = $cnd->{self::P_ZONES};

        $result->{self::P_DATE} = \XLite\Core\Converter::time();

        return $result;
    }

    /**
     * getSuitablePercentDiscountsCondition
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     * @param bool $isNext
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSuitablePercentDiscountsCondition($cnd, $isNext = false)
    {
        $result = $this->getSuitableDiscountsCondition($cnd, $isNext);
        $result->{self::P_TYPE} = \CDev\VolumeDiscounts\Model\VolumeDiscount::TYPE_PERCENT;

        return $result;
    }

    /**
     * getSuitableAbsoluteDiscountsCondition
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     * @param bool $isNext
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSuitableAbsoluteDiscountsCondition($cnd, $isNext = false)
    {
        $result = $this->getSuitableDiscountsCondition($cnd, $isNext);
        $result->{self::P_TYPE} = \CDev\VolumeDiscounts\Model\VolumeDiscount::TYPE_ABSOLUTE;

        return $result;
    }

    // }}}
}
