<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    /**
     * Allowable search params
     */
    public const P_PARTICIPATE_SALE   = 'participateSale';
    public const P_SALE_DISCOUNT      = 'saleDiscount';
    public const P_HAS_SALE_DISCOUNTS = 'hasSaleDiscounts';

    /**
     * Name of the calculated field - percent value.
     */
    public const PERCENT_CALCULATED_FIELD = 'percentValueCalculated';


    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndParticipateSale(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();

        $pricePercentCnd = new \Doctrine\ORM\Query\Expr\Andx();

        $pricePercentCnd->add('p.discountType = :discountTypePercent');
        $pricePercentCnd->add('p.salePriceValue > 0');

        $priceAbsoluteCnd = new \Doctrine\ORM\Query\Expr\Andx();

        $priceAbsoluteCnd->add('p.discountType = :discountTypePrice');
        $priceAbsoluteCnd->add('p.price > p.salePriceValue');

        $cnd->add($pricePercentCnd);
        $cnd->add($priceAbsoluteCnd);

        if (!$countOnly) {
            $queryBuilder->addSelect(
                'IFELSE(p.discountType = :discountTypePercent, p.salePriceValue, 100 - 100 * p.salePriceValue / p.price) ' . static::PERCENT_CALCULATED_FIELD
            );
        }

        $queryBuilder->andWhere('p.participateSale = :participateSale')
            ->andWhere($cnd)
            ->setParameter('participateSale', $value)
            ->setParameter('discountTypePercent', \CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PERCENT)
            ->setParameter('discountTypePrice', \CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PRICE);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndSaleDiscount(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('p.participateSale = false');

        if ($value instanceof \CDev\Sale\Model\SaleDiscount) {
            if ($value->getSpecificProducts()) {
                $queryBuilder->linkLeft('p.saleDiscountProducts', 'sdp');
                $queryBuilder->andWhere('sdp.saleDiscount = :saleDiscount')
                    ->setParameter('saleDiscount', $value);
            } else {
                if (!$value->getCategories()->isEmpty()) {
                    $queryBuilder->linkLeft('p.categoryProducts', 'cp')
                        ->linkLeft('cp.category', 'c');
                    $queryBuilder->andWhere('c IN (:saleCategories)')
                        ->setParameter('saleCategories', $value->getCategories());
                }
                if (!$value->getProductClasses()->isEmpty()) {
                    $queryBuilder->andWhere('p.productClass IN (:saleProductClasses)')
                        ->setParameter('saleProductClasses', $value->getProductClasses());
                }
            }
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndHasSaleDiscounts(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_array($value)) {
            /** @var $v \CDev\Sale\Model\SaleDiscount */
            foreach ($value as $k => $v) {
                $and = $queryBuilder->expr()->andX();

                if ($hasCategories = !$v->getCategories()->isEmpty()) {
                    $queryBuilder
                        ->linkLeft('p.categoryProducts', 'cp')
                        ->linkLeft('cp.category', 'c');
                    $and->add('c IN (:saleCategories' . $k . ')');
                    $queryBuilder->setParameter('saleCategories' . $k . '', $v->getCategories());
                }

                if ($hasProductClasses = !$v->getProductClasses()->isEmpty()) {
                    $and->add('p.productClass IN (:saleProductClasses' . $k . ')');
                    $queryBuilder->setParameter('saleProductClasses' . $k . '', $v->getProductClasses());
                }

                if ($hasSpecificProducts = $v->getSpecificProducts()) {
                    $specificProducts = array_map(static function ($saleDiscount) {
                        /** @var $saleDiscount \CDev\Sale\Model\SaleDiscountProduct */
                        return $saleDiscount->getProduct()->getProductId();
                    }, $v->getSaleDiscountProducts()->toArray());

                    $and->add('p.product_id IN (:specificProducts' . $k . ')');
                    $queryBuilder->setParameter('specificProducts' . $k . '', $specificProducts);
                }

                if ($hasCategories || $hasProductClasses || $hasSpecificProducts) {
                    $and->add('p.participateSale = false');
                    $queryBuilder->orWhere($and);
                }
            }
        }
    }
}
