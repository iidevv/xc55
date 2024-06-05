<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Add to new
 */
class AddToNew extends \XLite\View\FormField\Input\Checkbox\Switcher
{
    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return trim(parent::getDefaultWrapperClass() . ' add-to-new');
    }

    /**
     * Get 'Disabled' label
     *
     * @return string
     */
    protected function getDisabledLabel()
    {
        return 'Click if you want to add this value to new products or class’s assigns automatically';
    }

    /**
     * Get 'Enabled' label
     *
     * @return string
     */
    protected function getEnabledLabel()
    {
        return 'Click if you do not want to add this value to new products or class’s assigns automatically';
    }
}
