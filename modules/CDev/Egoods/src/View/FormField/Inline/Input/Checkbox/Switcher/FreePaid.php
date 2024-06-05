<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Switcher
 */
class FreePaid extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'CDev\Egoods\View\FormField\Input\Checkbox\FreePaid';
    }
}
