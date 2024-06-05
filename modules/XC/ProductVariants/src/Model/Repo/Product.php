<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model\Repo;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;

/**
 * Product model repository
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Model\Repo\Product
{
    public const VARIANT_SKU_FIELD = 'pv.sku';

    /**
     * Add inventory condition to search in-stock products
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function prepareCndInventoryIn(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->linkLeft('p.variants', 'pv');

        $orCnd = new Orx([
            'p.inventoryEnabled = :disabled',
            'pv.amount > :zero',
            new Andx([
                'p.amount > :zero',
                new Orx([
                    'pv.id IS NULL',
                    'pv.defaultAmount = true'
                ])
            ]),
        ]);

        $queryBuilder->andWhere($orCnd)
            ->setParameter('disabled', false)
            ->setParameter('zero', 0);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        parent::prepareCndSubstring($queryBuilder, $value);

        $queryBuilder->linkLeft('p.variants', 'pv');
    }

    /**
     * Return fields set for SKU search
     *
     * @return array
     */
    protected function getSubstringSearchFieldsBySKU()
    {
        return array_merge(
            parent::getSubstringSearchFieldsBySKU(),
            [
                static::VARIANT_SKU_FIELD,
            ]
        );
    }

    /**
     * Assign prica range-based search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param float                      $min          Minimum
     * @param float                      $max          Maximum
     *
     * @return void
     */
    protected function assignPriceRangeCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $min, $max)
    {
        if (!\XLite::isAdminZone() && \XC\ProductVariants\Main::isDisplayPriceAsRange()) {
            if ($min !== null) {
                $queryBuilder->andWhere($this->getCalculatedField($queryBuilder, 'maxPrice') . ' >= :minPrice')
                    ->setParameter('minPrice', (float) $min);
            }

            if ($max !== null) {
                $queryBuilder->andWhere($this->getCalculatedField($queryBuilder, 'minPrice') . ' <= :maxPrice')
                    ->setParameter('maxPrice', (float) $max);
            }
        } elseif ($min !== null || $max !== null) {
            parent::assignPriceRangeCondition($queryBuilder, $min, $max);
        }
    }

    /**
     * Define calculated minimal price definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedMinPriceDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        if (
            $profile
            && $profile->getMembership()
        ) {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMinPrice',
                'WITH',
                'qdMinPrice.membership = :qdMembership'
            )->setParameter('qdMembership', $profile->getMembership());
        } else {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMinPrice',
                'WITH',
                'qdMinPrice.membership is null'
            );
        }

        return 'qdMinPrice.minPrice';
    }

    /**
     * Define calculated maximal price definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedMaxPriceDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        if (
            $profile
            && $profile->getMembership()
        ) {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMaxPrice',
                'WITH',
                'qdMaxPrice.membership = :qdMembership'
            )->setParameter('qdMembership', $profile->getMembership());
        } else {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMaxPrice',
                'WITH',
                'qdMaxPrice.membership is null'
            );
        }

        return 'qdMaxPrice.maxPrice';
    }

    /**
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder
     */
    protected function definePriceRangeDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin('p.variants', 'pvp', 'WITH', 'pvp.defaultPrice = 0')
            ->addSelect('IFELSE(MIN(pvp.price) > p.price OR MIN(pvp.price) is null, p.price, MIN(pvp.price)) as min_price')
            ->addSelect('IFELSE(MAX(pvp.price) < p.price OR MAX(pvp.price) is null, p.price, MAX(pvp.price)) as max_price');
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            [$sort, $order] = $this->getSortOrderValue($value);

            if ($sort === 'p.price' && !\XLite::isAdminZone() && \XC\ProductVariants\Main::isDisplayPriceAsRange()) {
                $sort = $this->getCalculatedFieldAlias($queryBuilder, 'minPrice');

                $queryBuilder->addOrderBy($sort, $order);
                $queryBuilder->addOrderBy('qdMinPrice.maxPrice', $order);
            } elseif ($sort === 'p.clear_price_range') {
                $this->definePriceRangeDQL($queryBuilder);

                $queryBuilder->addOrderBy('min_price', $order);
                $queryBuilder->addOrderBy('max_price', $order);
            } else {
                parent::prepareCndOrderBy($queryBuilder, $value);
            }
        }
    }
}
