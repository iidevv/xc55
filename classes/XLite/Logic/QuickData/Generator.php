<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\QuickData;

/**
 * Quick data generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XLite\Logic\QuickData\Step\Products',
        ];
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'quickData';
    }

    // }}}
}
