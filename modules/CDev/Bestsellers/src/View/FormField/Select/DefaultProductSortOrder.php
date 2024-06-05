<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\View\FormField\Select;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class DefaultProductSortOrder extends \XLite\View\FormField\Select\DefaultProductSortOrder
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return parent::getDefaultOptions()
        + [
            'bought' => static::t('Sales'),
        ];
    }
}
