<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\StickyPanel\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Search extends \XLite\View\StickyPanel\Product\Admin\AAdmin
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['sale'] = [
            'class'    => 'CDev\Sale\View\Button\Dropdown\ProductSale',
            'params'   => [
                'label'         => '',
                'style'         => 'always-enabled more-action icon-only hide-on-disable',
                'icon-style'    => 'fa fa-percent',
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}
