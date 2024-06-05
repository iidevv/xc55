<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\FormField\Select;

/**
 * Navigation selector
 */
class Navigation extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'buttonsOnly'          => static::t('next and previous buttons'),
            'paginationOnly'       => static::t('pagination'),
            'paginationAndButtons' => static::t('next and previous buttons and pagination'),
        ];
    }
}
