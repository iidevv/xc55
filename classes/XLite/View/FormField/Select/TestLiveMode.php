<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\TestLiveMode
 */
class TestLiveMode extends \XLite\View\FormField\Select\Regular
{
    /**
     * Test/Live mode values
     */
    public const LIVE = 'live';
    public const TEST = 'test';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::LIVE => static::t('Test mode: Live'),
            static::TEST => static::t('Test mode: Test'),
        ];
    }
}
