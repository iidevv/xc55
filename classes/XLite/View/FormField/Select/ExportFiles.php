<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Export options : files
 */
class ExportFiles extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return 'local';
    }

    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'url'   => static::t('URLs'),
            'local' => static::t('local files'),
        ];
    }
}
