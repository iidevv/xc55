<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\View\Admin;

/**
 * Related products widget look selector
 */
class UpsellingProductsLook extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'list'  => static::t('List'),
            'grid'  => static::t('Grid'),
            'table' => static::t('Table'),
        ];
    }
}
