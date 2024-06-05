<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Model\Repo;

/**
 * Class video tour
 */
class VideoTours extends \XLite\Model\Repo\Base\I18n
{
    public const SEARCH_PRODUCT = 'product';
    public const SEARCH_ENABLED = 'enabled';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $productId = $value instanceof \XLite\Model\Product
            ? $value->getProductId()
            : (int) $value;

        if ($productId) {
            $queryBuilder->andWhere('v.product = :productId')
                ->setParameter('productId', $productId);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param bool                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, mixed $value): void
    {
        if (is_bool($value)) {
            $queryBuilder->andWhere('v.enabled = :enabledVideoTour')
                ->setParameter('enabledVideoTour', $value);
        }
    }
}