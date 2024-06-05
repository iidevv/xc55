<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * User activity type selector
 */
class UserActivityType extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''  => static::t('Please select one ...'),
            'R' => static::t('Registered'),
            'L' => static::t('Last logged in'),
        ];
    }
}
