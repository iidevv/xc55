<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAmountForFreeShipping\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Repo\Category
{
    public function findSpecialCategoriesByIds(array $ids): array
    {
        $result = [];
        $data = $this->defineFindSpecialCategoriesByIdsQuery($ids)->getResult();

        foreach ($data as $item) {
            $result[$item->getCategoryId()] = $item->getCategoryAmountShipping();
        }

        return $result;
    }

    protected function defineFindSpecialCategoriesByIdsQuery(array $ids)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.category_id IN (' . implode(',', $ids) . ')');
    }
}
