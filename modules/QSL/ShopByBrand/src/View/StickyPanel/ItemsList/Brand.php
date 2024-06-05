<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\StickyPanel\ItemsList;

use XLite\Core\Database;
use XLite\View\StickyPanel\ItemsListForm;

/**
 * Brands items list's sticky panel
 */
class Brand extends ItemsListForm
{
    protected function defineButtons(): array
    {
        $btns = parent::defineButtons();
        if (!Database::getRepo('QSL\ShopByBrand\Model\Brand')->count()) {
            unset($btns['save']);
        }
        return $btns;
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\QSL\ShopByBrand\Main';
    }
}
