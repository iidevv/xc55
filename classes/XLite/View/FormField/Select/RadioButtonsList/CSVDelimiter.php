<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList;

/**
 * CSV delimiter
 */
class CSVDelimiter extends \XLite\View\FormField\Select\RadioButtonsList\ARadioButtonsList
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ','   => static::t('Comma'),
            ';'   => static::t('Semicolon'),
        ];
    }
}
