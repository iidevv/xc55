<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\View\ItemsList\Model\Category
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/FeaturedProducts/items_list/category/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array_merge(parent::defineColumns(), [
            'featured_products' => [
                static::COLUMN_NAME     => static::t('Featured'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 250,
            ],
        ]);
    }
}
