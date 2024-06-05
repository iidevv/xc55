<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Model\Repo\Product\Attachment;

/**
 * Attachment history repository
 */
class AttachmentHistoryPoint extends \XLite\Model\Repo\ARepo
{
    public const P_PRODUCT = 'product';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $alias = $this->getMainAlias($queryBuilder);
        $queryBuilder->leftJoin($alias . '.attachment', 'attachment');
        if ($value instanceof \XLite\Model\Product) {
            $queryBuilder->andWhere('attachment.product = :product')
                         ->setParameter('product', $value);
        } else {
            $queryBuilder->leftJoin('attachment.product', 'product')
                         ->andWhere('product.product_id = :productId')
                         ->setParameter('productId', $value);
        }
    }
}
