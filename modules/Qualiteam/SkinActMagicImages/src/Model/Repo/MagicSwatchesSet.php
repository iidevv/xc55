<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Model\Repo;

use XLite\Model\Product;

class MagicSwatchesSet extends \XLite\Model\Repo\Base\I18n
{
    public const SEARCH_PRODUCT = 'product';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value): void
    {
        if ($value) {
            $queryBuilder->andWhere('m.product = :productMagicSwatchesSet')
                ->setParameter('productMagicSwatchesSet', $value);
        }
    }

    public function getColorSwatchesAttributeValueWithoutCurrentAttributeValue(Product $product, int $attributeValueId): ?array
    {
        return $this->createQueryBuilder()
            ->andWhere('m.product = :magicColorSwatchesProduct')
            ->andWhere('m.attributeValue != :currentAttributeValueId')
            ->setParameter('magicColorSwatchesProduct', $product)
            ->setParameter('currentAttributeValueId', $attributeValueId)
            ->getQuery()
            ->getResult();
    }
}