<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Category repository
 *
 * @Extender\Mixin
 */
abstract class Category extends \XLite\Model\Repo\Category
{

    /**
     * Get categories as dtos queryBuilder
     *
     * @param boolean $excludeRoot Do not include root category into the search result OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function getCategoriesAsDTOQueryBuilder($excludeRoot = true)
    {
        $queryBuilder = parent::getCategoriesAsDTOQueryBuilder($excludeRoot);

        $queryBuilder->addSelect('c.color as color');

        return $queryBuilder;
    }

}
