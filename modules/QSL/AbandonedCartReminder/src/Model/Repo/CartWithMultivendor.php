<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\AbandonedCartReminder", "XC\MultiVendor"})
 */
class CartWithMultivendor extends \XLite\Model\Repo\Cart
{
    /**
     * Links order items to the query.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function linkWithItems(QueryBuilder $queryBuilder, $value)
    {
        return $queryBuilder->linkInner('c.children', 'child')->linkInner('child.items')
               ->andWhere('items.item_id ' . ($value ? 'IS NOT NULL' : 'IS NULL'));
    }
}
