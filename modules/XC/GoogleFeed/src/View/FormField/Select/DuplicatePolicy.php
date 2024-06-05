<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\FormField\Select;

class DuplicatePolicy extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'export_as_separate' => static::t('Export duplicates as separate products'),
            'export_only_main' => static::t('Export only the original product')
        ];
    }
}
