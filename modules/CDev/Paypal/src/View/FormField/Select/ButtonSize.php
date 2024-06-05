<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Select;

class ButtonSize extends \XLite\View\FormField\Select\Regular
{
    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'small'      => 'Small',
            'medium'     => 'Medium',
            'large'      => 'Large',
            'responsive' => 'Responsive',
        ];
    }

    /**
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return '';
    }
}
