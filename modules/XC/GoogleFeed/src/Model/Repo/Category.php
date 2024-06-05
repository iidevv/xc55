<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Category repository
 * @Extender\Mixin
 */
abstract class Category extends \XLite\Model\Repo\Category
{
    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFeedGenerationQueryBuilder($position)
    {
        $qb = parent::defineFeedGenerationQueryBuilder($position);

        $qb->andWhere($qb->getMainAlias() . '.parent IS NOT NULL');

        return $qb;
    }
}
