<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

class CloudZoomMode extends \XLite\View\FormField\Select\ASelect
{
    public const MODE_INSIDE = 'inside';
    public const MODE_OUTSIDE = 'outside';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::MODE_INSIDE => static::t('Cloud Zoom mode Inside'),
            static::MODE_OUTSIDE => static::t('Cloud Zoom mode Outside'),
        ];
    }
}
