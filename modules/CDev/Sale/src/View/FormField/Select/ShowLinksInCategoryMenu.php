<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\FormField\Select;

/**
 * Menu selector
 */
class ShowLinksInCategoryMenu extends \XLite\View\FormField\Select\Regular
{
    public const TYPE_NOT_DISPLAY = 'not_display';
    public const TYPE_UNDER_CATEGORIES = 'under_categories';
    public const TYPE_ABOVE_CATEGORIES = 'above_categories';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::TYPE_NOT_DISPLAY => static::t('Do not display'),
            static::TYPE_UNDER_CATEGORIES => static::t('Display under categories list'),
            static::TYPE_ABOVE_CATEGORIES => static::t('Display above categories list'),
        ];
    }
}
