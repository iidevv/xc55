<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormField\Select;

/**
 * Class PriceInList
 */
class PriceInList extends \XLite\View\FormField\Select\Regular
{
    public const DISPLAY_DEFAULT = 'D';
    public const DISPLAY_RANGE = 'R';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::DISPLAY_DEFAULT => static::t('Default variant price'),
            static::DISPLAY_RANGE => static::t('Price range (min - max)'),
        ];
    }
}
