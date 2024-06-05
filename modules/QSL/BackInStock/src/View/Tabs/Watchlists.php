<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Watchlists extends \XLite\View\Tabs\Watchlists
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'back_in_stock_products',
                'back_in_stock_product_prices'
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['back_in_stock_products'] = [
            'weight' => 100,
            'title'  => static::t('Back in stock'),
            'widget' => 'QSL\BackInStock\View\ItemsList\Model\Product',
        ];

        $list['back_in_stock_product_prices'] = [
            'weight' => 200,
            'title'  => static::t('Price drop'),
            'widget' => 'QSL\BackInStock\View\ItemsList\Model\ProductPrice',
        ];

        return $list;
    }
}
