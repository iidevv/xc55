<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    public function findFreqBoughtProductsOrderIds($productId)
    {
        $result = [];
        $data = $this->defineFindFreqBoughtProductsOrderIdsQuery($productId)->getResult();

        foreach ($data as $item) {
            $result[] = $item['order_id'];
        }

        return $result;
    }

    protected function defineFindFreqBoughtProductsOrderIdsQuery($productId)
    {
        return $this->createQueryBuilder('oi')
            ->distinct()
            ->select('o.order_id')
            ->linkInner('oi.object', 'product')
            ->linkInner('oi.order', 'o')
            ->andWhere('product.product_id = :productId')
            ->setParameter('productId', $productId);
    }
}