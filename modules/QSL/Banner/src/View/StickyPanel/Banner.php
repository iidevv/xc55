<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\StickyPanel;

/**
 * Banners list's sticky panel
 */
class Banner extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Returns module settings URL.
     *
     * @return string
     */
    protected function getModuleSettingURL(): string
    {
        return $this->buildURL('module', '', ['moduleId' => 'QSL-Banner']);
    }
}
