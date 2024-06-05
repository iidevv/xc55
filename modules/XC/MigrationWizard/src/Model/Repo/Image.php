<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Variant image repository
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class Image extends \XC\ProductVariants\Model\Repo\Image\ProductVariant\Image
{
    /**
     * Count by product
     *
     * @param \XLite\Model\Product $product Product
     * @param string hash Image hash
     *
     * @return integer
     */
    public function countByProductAndHash(\XLite\Model\Product $product, $hash)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->linkInner('p.product_variant')
            ->andWhere('product_variant.product = :product')
            ->andWhere($qb->getMainAlias() . '.hash = :hash')
            ->setParameter('product', $product)
            ->setParameter('hash', $hash)
            ->count();
    }
}
