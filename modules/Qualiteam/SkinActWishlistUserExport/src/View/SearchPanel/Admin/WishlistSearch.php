<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\View\SearchPanel\Admin;


use XLite\Core\Session;

class WishlistSearch extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * Widget param names
     */
    public const PARAM_SUBSTRING = 'skuOrEmail';
    public const PARAM_DATE_RANGE = 'lastLoggedInDateRange';
    public const PARAM_SEARCH_NON_EMPTY_LISTS = 'searchNonEmpty';

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\ItemsList\AItemsListSearch';
    }

    /**
     * Define conditions.
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
                static::PARAM_SUBSTRING => [
                    static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                    \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('SkinActWishlistUserExport Product, SKU or customer info'),
                ],
                static::PARAM_DATE_RANGE => [
                    static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                    \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('SkinActWishlistUserExport Last logged in'),
                ],
            ];
    }

    protected function isUseFilter()
    {
        return true;
    }

    protected function canSaveFilter()
    {
        return false;
    }

    protected function hasActiveFilter()
    {
        return !empty(Session::getInstance()->QualiteamSkinActWishlistUserExportViewItemsListModelWishlistTable_search);
    }
}
