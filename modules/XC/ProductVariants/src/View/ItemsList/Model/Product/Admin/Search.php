<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\ItemsList\Model\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Products items list
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    public const SORT_BY_MODE_PRICE_RANGE  = 'p.clear_price_range';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_PRICE_RANGE => 'Price',
        ];

        parent::__construct($params);
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if (\XC\ProductVariants\Main::isDisplayPriceAsRange()) {
            $columns['price'][static::COLUMN_CLASS] = 'XC\ProductVariants\View\FormField\Inline\Input\Text\Product\Price';
            $columns['price'][static::COLUMN_SORT] = static::SORT_BY_MODE_PRICE_RANGE;
        }

        return $columns;
    }
}
