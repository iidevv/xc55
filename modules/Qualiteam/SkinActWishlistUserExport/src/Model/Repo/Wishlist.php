<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Wishlist extends \QSL\MyWishlist\Model\Repo\Wishlist
{
    public const SEARCH_SUBSTRING = 'skuOrEmail';
    public const SEARCH_DATE_RANGE = 'lastLoggedInDateRange';
    public const SEARCH_NON_EMPTY_LISTS = 'searchNonEmpty';


    protected function prepareCndSearchNonEmpty(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkLeft('w.wishlistLinks', 'wishlistLinks');
            $queryBuilder->andWhere('wishlistLinks IS NOT NULL');
        }
    }

    protected function prepareCndSkuOrEmail(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {

            $queryBuilder->linkLeft('w.customer', 'profile');
            $queryBuilder->linkLeft('w.wishlistLinks', 'wishlistLinks');
            $queryBuilder->linkLeft('wishlistLinks.parentProduct', 'product');
            $queryBuilder->linkLeft('product.translations', 'tr');

            $queryBuilder->where(
                'tr.name LIKE :searchVal
                           OR product.sku LIKE :searchVal
                           OR profile.searchFakeField LIKE :searchVal'
            )->setParameter('searchVal', '%' . $value . '%');
        }

        return $queryBuilder;
    }

    protected function prepareCndLastLoggedInDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {

            $queryBuilder->linkLeft('w.customer', 'profile');

            [$start, $end] = is_array($value) ? $value : \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('profile.last_login >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('profile.last_login <= :end')
                    ->setParameter('end', $end);
            }
        }

        return $queryBuilder;
    }

    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if ($value[0] === 'customerLogin') {
            $queryBuilder->linkLeft('w.customer', 'profile');
            $queryBuilder->addSelect('profile.login customerLogin');
            $queryBuilder->addOrderBy('customerLogin', $value[1]);
            return;
        }

        if ($value[0] === 'customerName') {
            $queryBuilder->linkLeft('w.customer', 'profile');
            $queryBuilder->addSelect('profile.searchFakeField customerName');
            $queryBuilder->addOrderBy('customerName', $value[1]);
            return;
        }

        if ($value[0] === 'wishlistLastUpdated') {
            $queryBuilder->addSelect(
                '(SELECT MAX(wl.creationDate)
                            FROM \QSL\MyWishlist\Model\WishlistLink wl
                        WHERE wl.wishlist = w.id) wishlistLastUpdated');

            $queryBuilder->addOrderBy('wishlistLastUpdated', $value[1]);
            return;
        }

        if ($value[0] === 'customerLastLogin') {
            $queryBuilder->linkLeft('w.customer', 'profile');
            $queryBuilder->addSelect('profile.last_login customerLastLogin');
            $queryBuilder->addOrderBy('customerLastLogin', $value[1]);
            return;
        }

        parent::prepareCndOrderBy($queryBuilder, $value);
    }

}