<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\View\FormField\Select;

class DepartmentSelect extends \XLite\View\FormField\Select\Regular
{
    public const DEP_ALL = 'All';
    public const DEP_PARTNERS  = 'Partners';
    public const DEP_MARKETING = 'Marketing';
    public const DEP_WEBDESIGN = 'Webdesign';
    public const DEP_SALES = 'Sales';

    public static function getDepartments()
    {
        return [
            static::DEP_ALL       => static::t('SkinActContactUsPage All'),
            static::DEP_PARTNERS  => static::t('SkinActContactUsPage Partners'),
            static::DEP_MARKETING => static::t('SkinActContactUsPage Marketing'),
            static::DEP_WEBDESIGN => static::t('SkinActContactUsPage Webdesign'),
            static::DEP_SALES     => static::t('SkinActContactUsPage Sales'),
        ];
    }

    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return static::getDepartments();
    }
}
