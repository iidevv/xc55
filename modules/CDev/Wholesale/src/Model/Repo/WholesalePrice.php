<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model\Repo;

class WholesalePrice extends \CDev\Wholesale\Model\Repo\Base\AWholesalePrice
{
    /**
     * Allowable search params
     */
    public const P_PRODUCT = 'product';

    /**
     * Get modifier types by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function getModifierTypesByProduct(\XLite\Model\Product $product)
    {
        $price = $this->createQueryBuilder('w')
            ->andWhere('w.product = :product')
            ->setParameter('product', $product)
            ->setMaxResults(1)
            ->getResult();

        return [
            'price'          => !empty($price),
            'wholesalePrice' => !empty($price),
        ];
    }

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
        if ($value instanceof \XLite\Model\Product) {
            $queryBuilder->andWhere('w.product = :product')
                ->setParameter('product', $value);
        } else {
            $queryBuilder->leftJoin('w.product', 'product')
                ->andWhere('product.product_id = :productId')
                ->setParameter('productId', $value);
        }
    }

    /**
     * Process contition
     *
     * @param \XLite\Core\CommonCell $cnd    Contition
     * @param mixed                  $object Object
     *
     * @return \XLite\Core\CommonCell
     */
    protected function processContition($cnd, $object)
    {
        $cnd->{self::P_PRODUCT} = $object;

        return $cnd;
    }
}
