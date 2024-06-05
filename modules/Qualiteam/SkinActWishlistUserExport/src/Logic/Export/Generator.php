<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\Logic\Export;

use Qualiteam\SkinActWishlistUserExport\View\SearchPanel\Admin\WishlistSearch as SearchPanel;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Session;

/**
 * Generator
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\Export\Generator
{
    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array_merge(
            parent::defineSteps(),
            [
                'Qualiteam\SkinActWishlistUserExport\Logic\Export\Step\Wishlist',
            ]
        );
    }

    protected function initialize()
    {
        parent::initialize();

        $conditions = Session::getInstance()->QualiteamSkinActWishlistUserExportViewItemsListModelWishlistTable_search;

        if (is_array($conditions) && !empty($conditions)) {

            $cnd = new \XLite\Core\CommonCell();

            foreach ($conditions as $k => $v) {
                $cnd->{$k} = $v;
            }

            $cnd->{SearchPanel::PARAM_SEARCH_NON_EMPTY_LISTS} = true;

            Session::getInstance()->QualiteamSkinActWishlistUserExportViewItemsListModelWishlistTable_processed = $cnd;
        }

    }
}
