<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy;

use Doctrine\ORM\QueryBuilder;

interface SortByContract
{
    public function addOrderBy(QueryBuilder $queryBuilder, string $order);

    public function __toString(): string;
}