<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\View\FormField\Select;

/**
 * Menu selector
 */
class Menu extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            '1' => static::t('a side box'),
            '0' => static::t('the main column'),
        ];
    }
}
