<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\StickyPanel\ItemsList;

/**
 * Swatches items list's sticky panel
 */
class Swatch extends \XLite\View\StickyPanel\ItemsListForm
{
    protected function getSaveWidgetStyle(): string
    {
        return parent::getSaveWidgetStyle() . ' hide-if-empty-list';
    }

    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'QSL-ColorSwatches']);
    }
}
