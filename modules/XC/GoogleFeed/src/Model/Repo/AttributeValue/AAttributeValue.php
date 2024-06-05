<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\Repo\AttributeValue;

use XCart\Extender\Mapping\Extender;

/**
 * Multiple attribute values repository
 * @Extender\Mixin
 */
abstract class AAttributeValue extends \XLite\Model\Repo\AttributeValue\AAttributeValue
{
    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function findNonMultipleAttributesGoogleFeed(\XLite\Model\Product $product)
    {
        $data = $this->createQueryBuilder('av')
            ->select('a.id')
            ->addSelect('COUNT(a.id) cnt')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product')
            ->andWhere('a.productClass is null OR a.productClass = :productClass')
            ->andWhere('a.googleShoppingGroup IN (:groups)')
            ->having('COUNT(a.id) = 1')
            ->setParameter('product', $product)
            ->setParameter('productClass', $product->getProductClass())
            ->setParameter('groups', \XLite\Model\Attribute::getGoogleShoppingGroups())
            ->addGroupBy('a.id')
            ->addOrderBy('a.position', 'ASC')
            ->getResult();

        $ids = [];
        if ($data) {
            foreach ($data as $v) {
                $ids[] = $v['id'];
            }
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Attribute')->getAttributesFeedData($product, $ids);
    }
}
