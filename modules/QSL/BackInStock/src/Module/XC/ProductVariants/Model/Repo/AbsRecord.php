<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class AbsRecord extends \QSL\BackInStock\Model\Repo\AbsRecord
{
    /**
     * Get record with specified conditions
     *
     * @param \XLite\Model\Product                                  $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     * @param \XLite\Model\Profile                                  $profile Profile OPTIONAL
     * @param string                                                $hash    Hash OPTIONAL
     *
     * @return \QSL\BackInStock\Model\Record
     */
    public function getRecordByVariantSet(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->defineGetRecordByVariantSetQuery($product, $variant, $profile, $hash)->getSingleResult();
    }

    /**
     * Get waited record with specified conditions
     *
     * @param \XLite\Model\Product                                  $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     * @param \XLite\Model\Profile                                  $profile Profile OPTIONAL
     * @param string
     *
     * @return \QSL\BackInStock\Model\Record
     */
    public function getWaitedRecordByVariantSet(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->defineGetWaitedRecordByVariantSetQuery($product, $variant, $profile, $hash)->getSingleResult();
    }

    /**
     * Count waiting records by product and variant
     *
     * @param \XLite\Model\Product $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     *
     * @return integer
     */
    public function countVariantWaiting(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null)
    {
        return $this->defineCountVariantWaitingQuery($product, $variant)->count();
    }

    /**
     * Define query builder for 'getRecordByVariantSet' method
     *
     * @param \XLite\Model\Product                                  $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     * @param \XLite\Model\Profile                                  $profile Profile OPTIONAL
     * @param string                                                $hash    Hash OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetRecordByVariantSetQuery(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND r.variant = :variant AND (r.profile = :profile OR r.hash = :hash)')
            ->setParameter('product', $product)
            ->setParameter('variant', $variant)
            ->setParameter('profile', $profile)
            ->setParameter('hash', $hash);
    }

    /**
     * Define query builder for 'getWaitedRecordByVariantSet' method
     *
     * @param \XLite\Model\Product                                  $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     * @param \XLite\Model\Profile                                  $profile Profile OPTIONAL
     * @param string                                                $hash    Hash OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetWaitedRecordByVariantSetQuery(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null, \XLite\Model\Profile $profile = null, $hash = null)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND r.variant = :variant AND (r.profile = :profile OR r.hash = :hash) AND r.state != :sent')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->setParameter('variant', $variant)
            ->setParameter('profile', $profile)
            ->setParameter('hash', $hash);
    }

    /**
     * Define query for 'countVariantWaiting' method
     *
     * @param \XLite\Model\Product $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountVariantWaitingQuery(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product AND r.state != :sent')
            ->andWhere('r.variant = :variant')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->setParameter('variant', $variant);
    }
}
