<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Repo\Category
{
    // {{{ Search
    public const P_SHOW_ON_HOME_PAGE = 'showOnHomePage';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb    Query builder to prepare
     * @param integer                                 $value Condition data
     *
     * @return void
     */
    protected function prepareCndShowOnHomePage(\XLite\Model\QueryBuilder\AQueryBuilder $qb, $value)
    {
        $qb->andWhere('c.showOnHomePage = :showOnHomePage')
           ->setParameter('showOnHomePage', $value);
    }

    // }}}
}
