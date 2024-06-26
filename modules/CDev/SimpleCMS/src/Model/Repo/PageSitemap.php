<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\XMLSitemap")
 */
class PageSitemap extends \CDev\SimpleCMS\Model\Repo\Page
{
    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineSitemapGenerationQueryBuilder($position)
    {
        $qb = parent::defineSitemapGenerationQueryBuilder($position);

        $qb->select($qb->getMainAlias() . '.id')
            ->andWhere($qb->getMainAlias() . '.enabled = true');

        $this->addCleanURLCondition($qb);

        return $qb;
    }

    /**
     * Add clean url if applicable
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function addCleanURLCondition(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        if (\CDev\SimpleCMS\Logic\Sitemap\Step\Page::isSitemapCleanUrlConditionApplicable()) {
            $joinCnd = 'cu.id = (SELECT MAX(cu2.id) FROM XLite\Model\CleanURL cu2 WHERE cu2.page = ' . $qb->getMainAlias() . ')';
            $qb->addSelect('cu.cleanURL')
                ->leftJoin('XLite\Model\CleanURL', 'cu', \Doctrine\ORM\Query\Expr\Join::WITH, $joinCnd);
        }

        return $qb;
    }
}
