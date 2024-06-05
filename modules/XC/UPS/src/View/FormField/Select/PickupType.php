<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\View\FormField\Select;

/**
 * Pickup type selector for settings page
 */
class PickupType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            '01' => static::t('Daily Pickup'),
            '03' => static::t('Customer counter'),
            '06' => static::t('One time pickup'),
            '07' => static::t('On call air'), // Will be ignored when negotiated rates are requested
            '19' => static::t('Letter center'),
            '20' => static::t('Air service center'),
        ];
    }
}
