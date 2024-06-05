<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend({"CDev\Sale","XC\ProductVariants"})
 */
class ProductList extends \XLite\Controller\Admin\ProductList
{
    /**
     * Cancel sale for provided product ids
     *
     * @param $ids
     */
    protected function cancelSaleByIds($ids)
    {
        parent::cancelSaleByIds($ids);

        $qb = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')->createQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->update('\XC\ProductVariants\Model\ProductVariant', $alias)
            ->set("{$alias}.defaultSale", $qb->expr()->literal(true))
            ->set("{$alias}.salePriceValue", 0)
            ->andWhere($qb->expr()->in("{$alias}.product", $ids))
            ->execute();
    }
}
