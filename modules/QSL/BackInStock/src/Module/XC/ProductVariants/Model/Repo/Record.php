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
class Record extends \QSL\BackInStock\Model\Repo\Record
{
    /**
     * Count waiting records by product (summary)
     *
     * @param \XLite\Model\Product $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant ProductVariant OPTIONAL
     *
     * @return integer
     */
    public function countSumVariantWaiting(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null)
    {
        $emails = $this->createQueryBuilder('r')
            ->select('r.email')
            ->andWhere('r.product = :product AND r.state != :sent')
            ->andWhere('r.variant = :variant')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->setParameter('variant', $variant)
            ->getArrayResult();
        $emailsPrices = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')->createQueryBuilder('r')
            ->select('r.email')
            ->andWhere('r.product = :product AND r.state != :sent')
            ->andWhere('r.variant = :variant')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->setParameter('variant', $variant)
            ->getArrayResult();

        $idx = [];

        foreach ($emails as $row) {
            $idx[$row['email']] = true;
        }

        foreach ($emailsPrices as $row) {
            $idx[$row['email']] = true;
        }

        return count($idx);
    }
}
