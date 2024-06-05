<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ContactUs\View\FormField;

use XLite\View\FormField\Select\RadioButtonsList\ARadioButtonsList;

class VersionSwitch extends ARadioButtonsList
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            2 => static::t('reCAPTCHA v2'),
            3 => static::t('reCAPTCHA v3'),
        ];
    }
}
