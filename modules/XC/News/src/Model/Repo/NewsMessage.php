<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Model\Repo;

class NewsMessage extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Alternative record identifiers
     *
     * @var   array
     */
    protected $alternativeIdentifier = [
        ['cleanURL'],
    ];

    /**
     * Find product by clean URL
     *
     * @param string $url Clean URL
     *
     * @return \XC\News\Model\NewsMessage
     */
    public function findOneByCleanURL($url)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.cleanURL = :url')
            ->setParameter('url', $url)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    // {{{ Search

    public const SEARCH_NAME    = 'name';
    public const SEARCH_ENABLED =  'enabled';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('translations.name = :name')
                ->setParameter('name', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('n.enabled = :true AND n.date < :time')
                ->setParameter('true', true)
                ->setParameter('time', \XLite\Core\Converter::time());
        }
    }

    /**
    * Prepare next and previous news query
    *
    * @param \XC\News\Model\NewsMessage $model Model to prepare
    *
    * @return \Doctrine\ORM\QueryBuilder
    */
    protected function defineSiblingsByNews($model)
    {
        return $this->createQueryBuilder()
            ->andWhere('n.id != :current')
            ->setParameter('date', $model->getDate())
            ->setParameter('current', $model->getId())
            ->setMaxResults(1);
    }

    /**
    * Prepare next and previous news
    *
    * @param \XC\News\Model\NewsMessage $model Model to prepare
    *
    * @return array
    */
    public function findSiblingsByNews(\XC\News\Model\NewsMessage $model)
    {
        $or = new \Doctrine\ORM\Query\Expr\Orx();
        $or->add('n.date < :date');
        $or->add('n.id < :current AND n.date = :date');
        $previous = $this->defineSiblingsByNews($model)
            ->orderBy('n.date', 'desc')
            ->addOrderBy('n.id', 'asc')
            ->andWhere($or)
            ->andWhere('n.enabled = :true AND n.date < :current_time')
            ->setParameter('true', true)
            ->setParameter('current_time', \XLite\Core\Converter::time())
            ->getSingleResult();

        $or = new \Doctrine\ORM\Query\Expr\Orx();
        $or->add('n.date > :date');
        $or->add('n.id > :current AND n.date = :date');

        $next = $this->defineSiblingsByNews($model)
            ->orderBy('n.date', 'asc')
            ->addOrderBy('n.id', 'desc')
            ->andWhere($or)
            ->andWhere('n.enabled = :true AND n.date < :current_time')
                ->setParameter('true', true)
                ->setParameter('current_time', \XLite\Core\Converter::time())
            ->getSingleResult();

        return [
            $previous,
            $next
        ];
    }

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

        $this->prepareCndEnabled($qb, true, true);
        $qb->select($qb->getMainAlias() . '.id');

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
            $joinCnd = 'cu.id = (SELECT MAX(cu2.id) FROM XLite\Model\CleanURL cu2 WHERE cu2.newsMessage = ' . $qb->getMainAlias() . ')';
            $qb->addSelect('cu.cleanURL')
                ->linkLeft('XLite\Model\CleanURL', 'cu', \Doctrine\ORM\Query\Expr\Join::WITH, $joinCnd);
        }

        return $qb;
    }

    /**
     * Count as sitemaps links
     *
     * @return integer
     */
    public function countAsSitemapsLinks()
    {
        $qb = $this->defineCountQuery();
        $this->prepareCndEnabled($qb, true, true);

        return $qb->count();
    }

    /**
     * Find one as sitemap link
     *
     * @param integer $position Position
     *
     * @return \XC\News\Model\NewsMessage
     */
    public function findOneAsSitemapLink($position)
    {
        $qb = $this->createPureQueryBuilder();
        $this->prepareCndEnabled($qb, true, true);

        return $qb
            ->setMaxResults(1)
            ->setFirstResult($position)
            ->getSingleResult();
    }

    // }}}
}
