<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OneClickUpsellAfterCheckout\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Upselling Product repository
 * @Extender\Mixin
 */
class UpsellingProduct extends \XC\Upselling\Model\Repo\UpsellingProduct
{
    use ExecuteCachedTrait;

    public const SEARCH_ORDER_NUMBER = 'orderNumber';
    public const NUMBERS_DELIMITER   = ',';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb    Query builder to prepare
     * @param string                                  $value Condition data
     */
    protected function prepareCndOrderNumber(\XLite\Model\QueryBuilder\AQueryBuilder $qb, $value)
    {
        $orderProductIds = $this->getOrderProductIds($value);

        $f = $this->getMainAlias($qb);
        if (!empty($orderProductIds)) {
            $qb->linkInner($f . '.parentProduct', 'parentProduct')
               ->linkInner($f . '.product', 'p')
               ->andWhere($qb->expr()->in('parentProduct.product_id', $orderProductIds))
               ->andWhere($qb->expr()->notIn('p.product_id', $orderProductIds))
               ->groupBy($f . '.id');
        } else {
            $qb->andWhere($f . '.id IS NULL');
        }
    }

    /**
     * @param string $orderNumbers allowed formats: integer or imploded like: 6,7,8
     *
     * @return array
     */
    protected function getOrderProductIds($orderNumbers)
    {
        return $this->executeCachedRuntime(static function () use ($orderNumbers) {
            $orderProductIds = \XLite\Core\Database::getRepo('XLite\Model\Order')
               ->createQueryBuilder('o')
               ->select('object.product_id')
               ->linkInner('o.items', 'items')
               ->linkInner('items.object', 'object')
               ->andWhere('o.orderNumber IN (:orderNumber)')
               ->setParameter('orderNumber', explode(self::NUMBERS_DELIMITER, $orderNumbers))
               ->getResult();

            return array_column($orderProductIds, 'product_id');
        }, $orderNumbers);
    }
}
