<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy;

use Doctrine\ORM\QueryBuilder;

class MobilePosition implements SortByContract
{
    public function addOrderBy(QueryBuilder $queryBuilder, string $order)
    {
        $queryBuilder->addOrderBy($queryBuilder->getMainAlias() . '.mobile_position', $order);
    }

    public function __toString(): string
    {
        return 'mobile_position';
    }
}