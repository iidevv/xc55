<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\FormField\Select;

/**
 * Delivery way type selector
 */
class DeliveryWayType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''    => static::t('Not specified'),
            'HFP' => static::t('Card (hold) for pick up'),
            'LAD' => static::t('Leave at door'),
            'DNS' => static::t('Do not safe drop'),
        ];
    }
}
