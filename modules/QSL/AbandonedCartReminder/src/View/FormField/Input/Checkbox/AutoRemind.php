<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormField\Input\Checkbox;

/**
 * Switch
 */
class AutoRemind extends \XLite\View\FormField\Input\Checkbox\Switcher
{
    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/AbandonedCartReminder/form_field';
    }

    /**
     * Get 'Disable' label
     *
     * TODO: remove this method after the release of X-Cart 5.2.7
     *
     * @return string
     */
    protected function getDisabledLabel()
    {
        return 'Disabled';
    }

    /**
     * Get 'Enable' label
     *
     * TODO: remove this method after the release of X-Cart 5.2.7
     *
     * @return string
     */
    protected function getEnabledLabel()
    {
        return 'Enabled';
    }
}
