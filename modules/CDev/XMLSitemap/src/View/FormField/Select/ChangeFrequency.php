<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\View\FormField\Select;

/**
 * Change frequency selector
 */
class ChangeFrequency extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'always'  => static::t('always'),
            'hourly'  => static::t('hourly'),
            'daily'   => static::t('daily'),
            'weekly'  => static::t('weekly'),
            'monthly' => static::t('monthly'),
            'yearly'  => static::t('yearly'),
            'never'   => static::t('never'),
        ];
    }
}
