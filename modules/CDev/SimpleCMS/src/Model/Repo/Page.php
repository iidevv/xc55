<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model\Repo;

class Page extends \XLite\Model\Repo\Base\I18n
{
    /**
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * @var array
     */
    protected $alternativeIdentifier = [
        ['cleanURL'],
    ];

    /**
     * @return integer
     */
    public function getMaxPosition()
    {
        $qb = $this->createQueryBuilder('page');
        return $qb->select('MAX(page.position)')->getSingleScalarResult();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            if (is_array($value)) {
                $queryBuilder->andWhere($alias . '.type IN (\'' . implode("','", $value) . '\')');
            } else {
                $queryBuilder->andWhere($alias . '.type = :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Count pages as sitemaps links
     *
     * @return integer
     */
    public function countPagesAsSitemapsLinks()
    {
        return $this->defineCountQuery()
            ->andWhere('p.enabled = true')
            ->count();
    }

    /**
     * Find one as sitemap link
     *
     * @param integer $position Position
     *
     * @return \CDev\SimpleCMS\Model\Page
     */
    public function findOneAsSitemapLink($position)
    {
        return $this->createPureQueryBuilder()
            ->andWhere('p.enabled = true')
            ->setMaxResults(1)
            ->setFirstResult($position)
            ->getSingleResult();
    }

    /**
     * @return Page[]|null
     */
    public function getSitemapPages()
    {
        return $this->createQueryBuilder()
            ->where('p.enabled = true')
            ->andWhere("p.type = 'content' OR p.frontUrl = '?target=contact_us'")
            ->orderBy('p.position', 'ASC')
            ->getResult();
    }
}
