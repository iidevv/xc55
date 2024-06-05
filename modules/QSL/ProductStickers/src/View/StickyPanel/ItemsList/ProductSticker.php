<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\StickyPanel\ItemsList;

use XLite\View\StickyPanel\ItemsListForm;

/**
 * Special offers items list's sticky panel
 */
class ProductSticker extends ItemsListForm
{
    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'QSL-ProductStickers']);
    }

    protected function getSaveWidgetStyle(): string
    {
        return parent::getSaveWidgetStyle() . ' hide-if-empty-list';
    }
}
