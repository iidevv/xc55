<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

class ApplyToBilling extends \XLite\View\FormField\Input\Checkbox\Enabled
{
    /**
     * Get default value
     *
     * @return string
     */
    protected function getDefaultHiddenValue()
    {
        return '0';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Apply to billing address');
    }
}
