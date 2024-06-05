<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\View\FormField\Select;

/**
 * Use rate type selector for settings page
 */
class UseRateType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'Rate'               => 'Retail Pricing',
            'CommercialRate'     => 'Commercial Base Pricing',
            'CommercialPlusRate' => 'Commercial Plus Pricing',
        ];
    }
}
