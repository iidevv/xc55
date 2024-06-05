<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Category repository
 *
 * @Extender\Mixin
 * @Extender\Depend("!XC\MultiVendor")
 */
abstract class Category extends \XLite\Model\Repo\Category
{
    /**
     * Check if display number of products
     *
     * @return boolean
     */
    protected function isShowNumberOfProducts()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_product_num;
    }

    /**
     * is multicolumn layout selected
     *
     * @return boolean
     */
    protected function isMulticolView()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_use_multicolumn;
    }

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

        if ($this->isMulticolView()) {
            $queryBuilder->addSelect('c.flyoutColumns as flyoutColumns');
        }

        $queryBuilder->addSelect('image.id as image_id');
        $queryBuilder->linkLeft('c.image');

        if ($this->isShowNumberOfProducts()) {
            $queryBuilder->addSelect('COUNT(DISTINCT categoryProducts) as productsNum')
                ->linkLeft('c.categoryProducts', 'categoryProducts')
                ->linkLeft(
                    'categoryProducts.product',
                    'categoryProductsProduct',
                    'WITH',
                    'categoryProductsProduct.enabled = :enabled'
                )
                ->setParameter('enabled', true);

            if (\XLite\Core\Config::getInstance()->General->show_out_of_stock_products === 'directLink' || \XLite\Core\Config::getInstance()->General->show_out_of_stock_products === 'searchOnly') {
                $queryBuilder->andWhere('(categoryProductsProduct.inventoryEnabled = false OR categoryProductsProduct.amount > 0 OR categoryProductsProduct.product_id IS NULL)');
            }

            $this->addProductsMembershipCondition($queryBuilder, 'categoryProductsProduct');
        }

        return $queryBuilder;
    }

    /**
     * Adds additional condition to the query for checking if product is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addProductsMembershipCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->getMembershipCondition()) {
            $alias = $alias ?: $this->getDefaultAlias();
            $membership = \XLite\Core\Auth::getInstance()->getMembershipId();

            if ($membership) {
                $queryBuilder->leftJoin($alias . '.memberships', 'productsMembership')
                    ->andWhere('productsMembership.membership_id = :membershipId OR productsMembership.membership_id IS NULL')
                    ->setParameter('membershipId', \XLite\Core\Auth::getInstance()->getMembershipId());
            } else {
                $queryBuilder->leftJoin($alias . '.memberships', 'productsMembership')
                    ->andWhere('productsMembership.membership_id IS NULL');
            }
        }
    }
}
