<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList;

/**
 * Yes / No radio buttons list
 */
class YesNo extends ARadioButtonsList
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'Y'   => static::t('Yes'),
            'N'   => static::t('No'),
        ];
    }
}
