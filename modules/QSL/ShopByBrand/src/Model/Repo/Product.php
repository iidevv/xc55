<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated product repository class.
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    /**
     * Allowable search params
     */
    public const P_BRAND_ID = 'brandId';

    /**
     * Return list of handling search params
     *
     * @return array
     */
    protected function getHandlingSearchParams()
    {
        $params   = parent::getHandlingSearchParams();
        $params[] = self::P_BRAND_ID;

        return $params;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     */
    protected function prepareCndBrandId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $brand = (is_object($value) && $value instanceof \QSL\ShopByBrand\Model\Brand)
                ? $value
                : \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->find(intval($value));

            $option = $brand ? $brand->getOption() : null;

            if ($option) {
                $queryBuilder->linkInner('p.attributeValueS', 'brandAttributeValue')
                    ->linkInner('brandAttributeValue.attribute_option', 'brandOption')
                    ->andWhere('brandOption.id = :brandOptionId')
                    ->setParameter('brandOptionId', $option->getId());
            }

            if ($brand) {
                $queryBuilder->leftJoin(
                    'QSL\ShopByBrand\Model\BrandProducts',
                    'bp',
                    'with',
                    'p.product_id = bp.product AND bp.brand = :brand'
                )->setParameter('brand', $brand);
            }
        }
    }
}
