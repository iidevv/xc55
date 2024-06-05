<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\StickyPanel\ItemsList;

/**
 * Sticky Panel widget for Cart Reminders page.
 */
class Reminder extends \XLite\View\StickyPanel\ItemsListForm
{
    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'QSL-AbandonedCartReminder']);
    }
}
