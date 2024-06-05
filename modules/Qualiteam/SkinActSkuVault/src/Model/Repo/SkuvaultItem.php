<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model\Repo;

use Doctrine\ORM\Query\Expr\Join;
use QSL\ShopByBrand\Model\Brand;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Product;
use XLite\Model\Repo\ARepo;

class SkuvaultItem extends ARepo
{
    public function deleteNotSyncedItems()
    {
        $ids = $this->createQueryBuilder('si')
            ->leftJoin(Product::class, 'p', Join::WITH, 'p.product_id = si.productId')
            ->leftJoin(AttributeValueSelect::class, 'avs', Join::WITH, 'si.productId = avs.product')
            ->leftJoin(Brand::class, 'b', Join::WITH, 'avs.attribute_option = b.option')
            ->andWhere('p.skipSyncToSkuvault = :p_skipSyncToSkuvault or b.skipSyncToSkuvault = :b_skipSyncToSkuvault or p.appointmentMembership IS NOT NULL')
            ->setParameter('p_skipSyncToSkuvault', 1)
            ->setParameter('b_skipSyncToSkuvault', 1)
            ->select('si.productId')
            ->getQuery()->getSingleColumnResult();

        if (!empty($ids)) {
            $this->createQueryBuilder('si')
                ->andWhere('si.productId in (:ids)')
                ->setParameter('ids', $ids)
                ->delete()
                ->getQuery()
                ->execute();
        }
    }
}
