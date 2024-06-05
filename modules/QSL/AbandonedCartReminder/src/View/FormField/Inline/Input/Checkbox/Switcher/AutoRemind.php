<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Enable/disable switcher for reminder list.
 */
class AutoRemind extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\Enabled
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'QSL\AbandonedCartReminder\View\FormField\Input\Checkbox\AutoRemind';
    }
}
