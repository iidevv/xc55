<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\StickyPanel\ItemsList;

/**
 * Records items list's sticky panel
 */
class SingleSettingLink extends \XLite\View\StickyPanel\ItemsListForm
{
    protected function getSaveWidgetStyle(): string
    {
        return parent::getSaveWidgetStyle() . ' hide-if-empty-list';
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\QSL\BackInStock\Main';
    }
}
