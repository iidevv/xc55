<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model\Repo;

/**
 * Banner repository
 *
 */
class Banner extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */

    public const P_CATEGORY_ID       = 'categoryId';
    public const P_PAGE_ID           = 'pageId';
    public const P_MEMBERSHIP_ID     = 'membershipId';
    public const P_LOCATION          = 'location';
    public const P_HOMEPAGE          = 'homePage';
    public const P_PRODUCTS_PAGES    = 'productsPages';
    public const P_PARALLAX          = 'parallax';
    public const P_ENABLED           = 'enabled';

    /**
     * Default 'order by' field name
     *
     * @var   string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Get banners list
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getAllBanners()
    {
        return $this->findAllBanners()->getResult();
    }

    /**
     * Get last order by
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getLastOrderBy()
    {
        return $this->findLastOrderBy()->getSingleScalarResult();
    }

    /**
     * Find last id
     */
    public function getLastId()
    {
        return $this->createQueryBuilder()->select('MAX(b.id)')->getSingleScalarResult();
    }

    /**
     * Find last order by
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function findLastOrderBy()
    {
        return $this->createQueryBuilder()->select('MAX(b.position)');
    }

    /**
     * Find download items list
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function findAllBanners()
    {
        return $this->createQueryBuilder()->orderBy('b.position');
    }

    // {{{ Search


    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_object($value) && $value instanceof \XLite\Model\Category) {
            $value = $value->getCategoryId();
        }
        if ($value) {
            $queryBuilder->linkInner('b.categories', 'c')
                ->linkInner('c.category', 'c')
                ->andWhere('c.category_id = :categoryId')
                ->setParameter('categoryId', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndPageId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_object($value) && $value instanceof \CDev\SimpleCMS\Model\Page) {
            $value = $value->getPage()->getId();
        }
        if ($value) {
            $queryBuilder->linkInner('b.pages', 'p')
                ->linkInner('p.page', 'p')
                ->andWhere('p.id = :pageId')
                ->setParameter('pageId', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndNoMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndMembershipId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_object($value) && $value instanceof \XLite\Model\Membership) {
            $value = $value->getMembershipId();
        }

        if ($value) {
            $queryBuilder->linkLeft('b.memberships', 'm')
                ->linkLeft('m.membership', 'm')
                ->andWhere('m.membership_id = :membershipId OR m.membership_id IS NULL')
                ->setParameter('membershipId', $value);
        }
        if ($this->searchState['currentSearchCnd']->no_membership === true) {
            $queryBuilder->linkLeft('b.memberships', 'm')
                ->linkLeft('m.membership', 'm')
                ->andWhere('m.membership_id is NULL');
        }
    }

     /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLocation(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.location = :location')
                ->setParameter('location', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.id = :id')
                ->setParameter('id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndHomePage(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.home_page = true');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProductsPages(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.products_pages = true');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndParallax(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.parallax = true');
        }
    }


    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('b.enabled = true');
        }
    }
}
