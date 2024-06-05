<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Module\XC\ProductVariants\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 * @Extender\After ("QSL\OrderReports")
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    /**
     * @param array $dateRange
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryGetSegmentByProduct(array $dateRange)
    {
        $qb = parent::defineQueryGetSegmentByProduct($dateRange);

        $qb
            ->addSelect('oiv.variant_id AS variant_id')
            ->linkLeft('oi.variant', 'oiv')
            ->addGroupBy('oi.variant');

        return $qb;
    }
}
