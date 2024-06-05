<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\StickyPanel\ItemsList;

class SingleSettingLink extends \XLite\View\StickyPanel\ItemsListForm
{
    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'QSL-AbandonedCartReminder']);
    }

    protected function defineButtons()
    {
        // we need this code to force hide 'Save changes' button
        $list = parent::defineButtons();
        unset($list['save']);// we don't need 'Save changes' button

        return $list;
    }
}
