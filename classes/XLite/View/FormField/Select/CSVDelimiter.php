<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * CSV delimiter mode selector
 */
class CSVDelimiter extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ',' => ', (' . static::t('Comma') . ')',
            ';' => '; (' . static::t('Semicolon') . ')',
        ];
    }
}
