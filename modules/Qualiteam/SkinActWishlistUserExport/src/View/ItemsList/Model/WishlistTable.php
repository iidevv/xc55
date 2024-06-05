<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActWishlistUserExport\View\ItemsList\Model;


use Qualiteam\SkinActWishlistUserExport\View\SearchPanel\Admin\WishlistSearch as SearchPanel;
use XLite\Core\Converter;
use Qualiteam\SkinActWishlistUserExport\Model\Repo\Wishlist as Repo;
use XLite\Core\Database;

class WishlistTable extends \XLite\View\ItemsList\Model\Table
{

    protected $sortByModes = [
        'customerLogin' => 'SkinActWishlistUserExport login',
        'customerName' => 'SkinActWishlistUserExport name',
        'wishlistLastUpdated' => 'SkinActWishlistUserExport lastUpdated',
        'customerLastLogin' => 'SkinActWishlistUserExport lastLogin',
    ];

    protected function isSelectable()
    {
        return true;
    }

    protected function getPanelClass()
    {
        return '\Qualiteam\SkinActWishlistUserExport\View\StickyPanel\WishlistStickyPanel';
    }

    protected function defineRepositoryName()
    {
        return '\QSL\MyWishlist\Model\Wishlist';
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getLoginColumnValue(\XLite\Model\AEntity $entity)
    {
        return $entity->getCustomer()->getLogin();
    }

    protected function getNameColumnValue(\XLite\Model\AEntity $entity)
    {
        return $entity->getCustomer()->getName();
    }

    protected function getItemsCountColumnValue(\XLite\Model\AEntity $entity)
    {
        return $entity->getWishlistLinks()->count();
    }


    protected function getLastUpdatedColumnValue(\XLite\Model\AEntity $entity)
    {
        $qb = Database::getRepo('\QSL\MyWishlist\Model\WishlistLink')->createPureQueryBuilder();

        $maxCreationDate = (int)$qb->select('MAX(w.creationDate)')
            ->where('w.wishlist = :wishlist')
            ->setParameter('wishlist', $entity)
            ->getSingleScalarResult();

        if ($maxCreationDate === 0) {
            return static::t('SkinActWishlistUserExport empty list');
        }

        return Converter::formatTime($maxCreationDate);
    }


    protected function getLastLoginColumnValue(\XLite\Model\AEntity $entity)
    {
        return Converter::formatTime($entity->getCustomer()->getLastLogin());
    }

    protected function getSearchPanelClass()
    {
        return '\Qualiteam\SkinActWishlistUserExport\View\SearchPanel\Admin\WishlistSearch';
    }

    protected function getFormTarget()
    {
        return 'wishlist_table';
    }

    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{SearchPanel::PARAM_SEARCH_NON_EMPTY_LISTS} = true;

        return $result;
    }

    public static function getSearchParams()
    {
        return [
            Repo::SEARCH_SUBSTRING => SearchPanel::PARAM_SUBSTRING,
            Repo::SEARCH_DATE_RANGE => SearchPanel::PARAM_DATE_RANGE,
          //  Repo::SEARCH_NON_EMPTY_LISTS => SearchPanel::PARAM_SEARCH_NON_EMPTY_LISTS,
        ];
    }

    protected function defineColumns()
    {
        return [
            'login' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActWishlistUserExport login'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_LINK => 'profile',
                static::COLUMN_SORT => 'customerLogin',
            ],
            'name' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActWishlistUserExport name'),
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_MAIN => true,
                static::COLUMN_LINK => 'profile',
                static::COLUMN_SORT => 'customerName',
            ],
            'itemsCount' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActWishlistUserExport itemsCount'),
                static::COLUMN_ORDERBY => 300,
                static::COLUMN_LINK => 'wishlist',
            ],
            'lastUpdated' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActWishlistUserExport lastUpdated'),
                static::COLUMN_ORDERBY => 400,
                static::COLUMN_SORT => 'wishlistLastUpdated',
            ],
            'lastLogin' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActWishlistUserExport lastLogin'),
                static::COLUMN_ORDERBY => 500,
                static::COLUMN_SORT => 'customerLastLogin',
            ],
        ];

    }

    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        switch ($column[static::COLUMN_LINK]) {
            case 'wishlist':
                $result = \XLite\Core\Converter::buildURL(
                    'wishlist',
                    '',
                    ['profile_id' => $entity->getCustomer()->getProfileId()]
                );
                break;
            case 'profile':
                $result = \XLite\Core\Converter::buildURL(
                    'profile',
                    '',
                    ['profile_id' => $entity->getCustomer()->getProfileId()]
                );
                break;
            default:
                $result = '';
                break;
        }

        return $result;
    }

}