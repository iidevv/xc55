<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Product;

/**
 * Product availability status selector
 */
class AvailabilityStatus extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''         => static::t('Any availability status'),
            'enabled'  => static::t('Only enabled'),
            'disabled' => static::t('Only disabled'),
        ];
    }
}
