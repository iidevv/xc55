<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\AbandonedCartReminder", "XC\MultiVendor"})
 */
class OrderWithMultiVendor extends \XLite\Model\Repo\Order
{
    /**
     * Search recovered carts.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndRecovered(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            // Show/hide recovered orders
            $selfRef = $queryBuilder->getMainAlias();
            $queryBuilder->linkLeft("{$selfRef}.parent", 'parent');

            if ($value) {
                $orExpr = $queryBuilder->expr()->orX();

                $orExpr->add("{$selfRef}.recovered > 0");
                $orExpr->add('parent.recovered > 0');

                $queryBuilder->andWhere($orExpr);
            } else {
                $andExpr = $queryBuilder->expr()->andX();

                $andExpr->add("{$selfRef}.recovered = 0");
                $andExpr->add('parent.recovered = 0');

                $queryBuilder->andWhere($andExpr);
            }
        }
    }
}
