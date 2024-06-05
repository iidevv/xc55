<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\FlyoutCategoriesMenu\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Category repository
 * @Extender\Mixin
 */
abstract class Category extends \XLite\Model\Repo\Category
{
    /**
     * Check if display number of prducts
     *
     * @return boolean
     */
    protected function isShowProductNum()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num;
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

        if ($this->isShowProductNum()) {
            $queryBuilder->addSelect('COUNT(DISTINCT categoryProductsProduct.product_id) as productsCount')
                ->linkLeft('c.categoryProducts', 'categoryProducts');
            if (\XLite\Core\Config::getInstance()->General->show_out_of_stock_products === 'directLink' || \XLite\Core\Config::getInstance()->General->show_out_of_stock_products === 'searchOnly') {
                $queryBuilder->linkLeft(
                    'categoryProducts.product',
                    'categoryProductsProduct',
                    'WITH',
                    'categoryProductsProduct.enabled = :enabled and (categoryProductsProduct.inventoryEnabled = false OR categoryProductsProduct.amount > 0)'
                )
                    ->setParameter('enabled', true);
            } else {
                $queryBuilder->linkLeft(
                    'categoryProducts.product',
                    'categoryProductsProduct',
                    'WITH',
                    'categoryProductsProduct.enabled = :enabled'
                )
                    ->setParameter('enabled', true);
            }

            $this->addProductMembershipCondition($queryBuilder, 'categoryProductsProduct');
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
    protected function addProductMembershipCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->getMembershipCondition()) {
            $alias = $alias ?: $this->getDefaultAlias();
            $membership = \XLite\Core\Auth::getInstance()->getMembershipId();

            if ($membership) {
                $queryBuilder->leftJoin($alias . '.memberships', 'productMembership')
                    ->andWhere('productMembership.membership_id = :membershipId OR productMembership.membership_id IS NULL')
                    ->setParameter('membershipId', \XLite\Core\Auth::getInstance()->getMembershipId());
            } else {
                $queryBuilder->leftJoin($alias . '.memberships', 'productMembership')
                    ->andWhere('productMembership.membership_id IS NULL');
            }
        }
    }
}
