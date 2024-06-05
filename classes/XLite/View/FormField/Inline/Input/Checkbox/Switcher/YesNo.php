<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Switcher
 */
class YesNo extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Checkbox\YesNo';
    }
}
