<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\FormField\Select;

/**
 * Field to choose the order of products on brand pages.
 */
class BrandProductsSortOrder extends \XLite\View\FormField\Select\DefaultProductSortOrder
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();
        $options[\QSL\ShopByBrand\View\ItemsList\Product\Customer\Brand::SORT_BY_MODE_DEFAULT] = static::t('Recommended');
        unset($options['default']);

        return $options;
    }
}
