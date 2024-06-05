<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\StickyPanel\ItemsList;

use XLite\Core\Database;
use XLite\View\StickyPanel\ItemsListForm;

/**
 * Special offers items list's sticky panel
 */
class SpecialOffer extends ItemsListForm
{
    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\QSL\SpecialOffersBase\Main';
    }

    /**
     * Disable "save" button if the list is empty.
     */
    protected function defineButtons(): array
    {
        $list = parent::defineButtons();
        if (!Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer')->count()) {
            unset($list['save']);
        }
        return $list;
    }
}
