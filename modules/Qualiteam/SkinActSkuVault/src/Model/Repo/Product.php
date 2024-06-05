<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model\Repo;

use Doctrine\ORM\Query\Expr\Join;
use QSL\ShopByBrand\Model\Brand;
use XCart\Extender\Mapping\Extender;
use XLite\Model\AttributeValue\AttributeValueSelect;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    public function findProductIdsToSync()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin(AttributeValueSelect::class, 'avs', Join::WITH, 'p.product_id = avs.product')
            ->leftJoin(Brand::class, 'b', Join::WITH, 'avs.attribute_option = b.option')
            ->andWhere('p.isSkuvaultSynced = :isSkuvaultSynced')
            ->andWhere('p.skipSyncToSkuvault = :p_skipSyncToSkuvault OR p.skipSyncToSkuvault IS NULL')
            ->andWhere('b.skipSyncToSkuvault = :b_skipSyncToSkuvault OR b.skipSyncToSkuvault IS NULL')
            ->andWhere('p.appointmentMembership IS NULL')
            ->andWhere('p.isSkuvaultUpdateSynced = :isSkuvaultUpdateSynced')
            ->setParameter('isSkuvaultSynced', 1)
            ->setParameter('p_skipSyncToSkuvault', 0)
            ->setParameter('b_skipSyncToSkuvault', 0)
            ->setParameter('isSkuvaultUpdateSynced', 0)
            ->select('p.product_id')
            ->setMaxResults(100)
            ->getQuery()->getSingleColumnResult();
    }

    public function findProductIdsToCreateInSkuvault()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin(AttributeValueSelect::class, 'avs', Join::WITH, 'p.product_id = avs.product')
            ->leftJoin(Brand::class, 'b', Join::WITH, 'avs.attribute_option = b.option')
            ->andWhere('p.isSkuvaultSynced = :isSkuvaultSynced OR p.isSkuvaultSynced IS NULL')
            ->andWhere('p.skipSyncToSkuvault = :p_skipSyncToSkuvault OR p.skipSyncToSkuvault IS NULL')
            ->andWhere('b.skipSyncToSkuvault = :b_skipSyncToSkuvault OR b.skipSyncToSkuvault IS NULL')
            ->andWhere('p.appointmentMembership IS NULL')
            ->setParameter('isSkuvaultSynced', 0)
            ->setParameter('p_skipSyncToSkuvault', 0)
            ->setParameter('b_skipSyncToSkuvault', 0)
            ->select('p.product_id')
            ->setMaxResults(100)
            ->getQuery()->getSingleColumnResult();
    }
}
