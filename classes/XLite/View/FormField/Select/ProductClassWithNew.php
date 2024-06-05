<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Product class with new selector
 */
class ProductClassWithNew extends \XLite\View\FormField\Select\ProductClass
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return parent::getDefaultOptions() + [-1 => static::t('New product class')];
    }
}
