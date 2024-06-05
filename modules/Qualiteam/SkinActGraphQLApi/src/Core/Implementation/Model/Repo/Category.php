<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Model\Repo;

use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Class Category
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Model\Repo
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Category extends \XLite\Model\Repo\Category
{
    /**
     * Return number of products associated with the category
     *
     * @return array
     */
    public function getProductsCountsForMobileApi($categoryIds)
    {
        $cnd = new \XLite\Core\CommonCell();

        if ('directLink' !== \XLite\Core\Config::getInstance()->General->show_out_of_stock_products
            && !\XLite::isAdminZone()
        ) {
            $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = false;
        }

        $productsRepo = \XLite\Core\Database::getRepo('XLite\Model\Product');
        /** @var AQueryBuilder $qb */
        $qb = $productsRepo->search($cnd, ARepo::SEARCH_MODE_PREPARE_QUERY_BUILDER);

        $key = $productsRepo->getSearchPrimaryFields($qb);


        $qb->linkLeft('p.categoryProducts', 'cp')
            ->linkLeft('cp.category', 'c');

        $qb->resetDQLPart('select')
            ->resetDQLPart('groupBy')
            ->resetDQLPart('orderBy');

        $qb->addSelect('COUNT(DISTINCT ' . $key . ') as countValue')
            ->addSelect('c.category_id');

        $qb->andWhere('c.category_id IN (:categories)')
            ->setParameter('categories', $categoryIds);
        $qb->addGroupBy('c.category_id');

        $result = $qb->getQuery()->getScalarResult();

        if ($result && is_array($result)) {
            $result = array_combine(
                array_map(function($elem) {
                    return $elem['category_id'];
                }, $result),
                $result
            );
        }

        return $result;
    }

    /**
     * Return names of the categories
     *
     * @return array
     */
    public function getNamesForMobileApi($categoryIds)
    {
        $qb = $this->getCategoriesAsDTOQueryBuilder();

        $qb->select('translations.name');
        $qb->addSelect('c.category_id');

        $qb->andWhere('c.category_id IN (:categories)')
            ->setParameter('categories', $categoryIds);
        $qb->addGroupBy('c.category_id');

        $qb->indexBy('c', 'c.category_id');

        return $qb->getArrayResult();
    }

    /**
     * @param     $categoryId
     * @param int $from
     * @param int $size
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSubcategoriesForMobileAPI($categoryId, $from = 0, $size = 0)
    {
        $queryBuilder = $this->initializeQueryBuilder($this->createPureQueryBuilder());
        $queryBuilder
            ->andWhere($this->getDefaultAlias() . '.enabled = :enabled')
            ->setParameter('enabled', true);

        if ($categoryId) {
            $queryBuilder->innerJoin('c.parent', 'cparent')
                ->andWhere('cparent.category_id = :parentId')
                ->setParameter('parentId', $categoryId);

        } else {
            $queryBuilder->andWhere('c.parent IS NULL');
        }

        if ($from) {
            $queryBuilder->setFirstResult($from);
        }

        if ($size) {
            $queryBuilder->setMaxResults($size);
        }

        return $queryBuilder->getResult();
    }
}
