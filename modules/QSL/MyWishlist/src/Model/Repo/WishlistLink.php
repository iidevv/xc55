<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model\Repo;

/**
 * Wishlist links repository
 */
class WishlistLink extends \XLite\Model\Repo\ARepo
{
    /**
     *
     * @param type    $wishlist
     * @param boolean $countOnly
     *
     * @return mixed
     */
    public function getWishlistProducts($wishlist, $countOnly)
    {
        $qb = $this->createQueryBuilder('wl');

        $qb->andWhere('wl.wishlist = :wishlist')
            ->setParameter('wishlist', $wishlist)
            ->addOrderBy('wl.orderby', 'asc');

        if (!\XLite::isAdminZone()) {
            $qb
                ->innerJoin('wl.parentProduct', 'pr')
                ->andWhere('pr.enabled = :enabled')
                ->setParameter('enabled', true);
        }

        return $countOnly
            ? $this->searchWishlistCount($qb)
            : $this->searchWishlistResult($qb);
    }

    /**
     *
     * @param $wishlist
     *
     * @return mixed
     */
    public function getWishlistProductIds($wishlist)
    {
        $qb = $this->createQueryBuilder('wl');

        $qb->select('DISTINCT parentProduct.product_id')
            ->linkLeft('wl.parentProduct', 'parentProduct')
            ->andWhere('wl.wishlist = :wishlist')
            ->andWhere('parentProduct.enabled = :enabled')
            ->setParameter('wishlist', $wishlist)
            ->setParameter('enabled', true);

        return array_column($qb->getResult(), 'product_id');
    }

    /**
     * Search count only routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    protected function searchWishlistCount(\Doctrine\ORM\QueryBuilder $qb)
    {
        $qb->select('COUNT(DISTINCT wl.id)')
            ->innerJoin('wl.parentProduct', 'pr2')
            ->andWhere('pr2.enabled = :enabled')
            ->setParameter('enabled', true);

        return intval($qb->getSingleScalarResult());
    }

    /**
     * Search result routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    protected function searchWishlistResult(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->addGroupById($qb);

        return $qb->getOnlyEntities();
    }

    /**
     * Add 'Group By product_id' expression
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return void
     */
    protected function addGroupById(\Doctrine\ORM\QueryBuilder $qb)
    {
        $qb->addGroupBy('wl.id');
    }

    /**
     * Define the minimum order by from the wishlist
     *
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist Wishlist model
     *
     * @return integer
     */
    public function getMinimumOrderby($wishlist)
    {
        return intval($this
            ->createQueryBuilder('wl')
            ->select('wl.orderby')
            ->andWhere('wl.wishlist = :wishlist')
            ->addOrderBy('wl.orderby', 'asc')
            ->setParameter('wishlist', $wishlist)
            ->getSingleScalarResult());
    }

    /**
     * Define the maximum order by from the wishlist
     *
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist Wishlist model
     *
     * @return integer
     */
    public function getMaximumOrderby($wishlist)
    {
        return intval($this
            ->createQueryBuilder('wl')
            ->select('wl.orderby')
            ->andWhere('wl.wishlist = :wishlist')
            ->addOrderBy('wl.orderby', 'desc')
            ->setParameter('wishlist', $wishlist)
            ->getSingleScalarResult());
    }
}
