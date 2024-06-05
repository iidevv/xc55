<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Module\XC\ProductVariants\Model\Repo;

use Doctrine\ORM\Query\Expr\Join;
use QSL\ShopByBrand\Model\Brand;
use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\Repo\ProductVariant
{
    public function findVariantIdsToSync()
    {
        return $this->createQueryBuilder('pv')
            ->leftJoin(Product::class, 'p', Join::WITH, 'pv.product = p.product_id')
            ->leftJoin(AttributeValueSelect::class, 'avs', Join::WITH, 'p.product_id = avs.product')
            ->leftJoin(Brand::class, 'b', Join::WITH, 'avs.attribute_option = b.option')
            ->andWhere('p.isSkuvaultSynced = :isProductSkuvaultSynced')
            ->andWhere('pv.isSkuvaultSynced = :isProductVariantSkuvaultSynced')
            ->andWhere('p.skipSyncToSkuvault = :p_skipSyncToSkuvault OR p.skipSyncToSkuvault IS NULL')
            ->andWhere('b.skipSyncToSkuvault = :b_skipSyncToSkuvault OR b.skipSyncToSkuvault IS NULL')
            ->andWhere('p.appointmentMembership IS NULL')
            ->setParameter('isProductSkuvaultSynced', 1)
            ->setParameter('isProductVariantSkuvaultSynced', 1)
            ->setParameter('p_skipSyncToSkuvault', 0)
            ->setParameter('b_skipSyncToSkuvault', 0)
            ->select('pv.id')
            ->groupBy('pv.id')
            ->orderBy('pv.id', 'ASC')
            ->getQuery()->getSingleColumnResult();
    }

    public function findVariantIdsToCreateInSkuvault()
    {
        return $this->createQueryBuilder('pv')
            ->leftJoin(Product::class, 'p', Join::WITH, 'pv.product = p.product_id')
            ->leftJoin(AttributeValueSelect::class, 'avs', Join::WITH, 'p.product_id = avs.product')
            ->leftJoin(Brand::class, 'b', Join::WITH, 'avs.attribute_option = b.option')
            ->andWhere('p.isSkuvaultSynced = :isProductSkuvaultSynced')
            ->andWhere('pv.isSkuvaultSynced = :isProductVariantSkuvaultSynced')
            ->andWhere('p.skipSyncToSkuvault = :p_skipSyncToSkuvault OR p.skipSyncToSkuvault IS NULL')
            ->andWhere('b.skipSyncToSkuvault = :b_skipSyncToSkuvault OR b.skipSyncToSkuvault IS NULL')
            ->andWhere('p.appointmentMembership IS NULL')
            ->setParameter('isProductSkuvaultSynced', 1)
            ->setParameter('isProductVariantSkuvaultSynced', 0)
            ->setParameter('p_skipSyncToSkuvault', 0)
            ->setParameter('b_skipSyncToSkuvault', 0)
            ->select('pv.id')
            ->groupBy('pv.id')
            ->setMaxResults(100)
            ->orderBy('pv.id', 'ASC')
            ->getQuery()->getSingleColumnResult();
    }
}
