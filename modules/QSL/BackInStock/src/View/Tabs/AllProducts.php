<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
class AllProducts extends \XLite\View\Tabs\AllProducts
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

        if (Auth::getInstance()->hasRootAccess()) {
            $list['back_in_stock_products'] = [
                'weight'     => 600,
                'title'      => static::t('Watchlists'),
                'references' => [
                    ['target' => 'back_in_stock_product_prices'],
                ],
                'widget'     => 'QSL\BackInStock\View\Tabs\Watchlists',
            ];
        }

        return $list;
    }
}
