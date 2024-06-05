<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\StickyPanel\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Search product list sticky panel
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
        $list['facebook_feed'] = [
            'class'    => 'XC\FacebookMarketing\View\Button\Dropdown\FacebookSwitcher',
            'params'   => [
                'label'         => '',
                'style'         => 'more-action icon-only hide-on-disable hidden',
                'icon-style'    => 'fa fa-facebook-official',
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}
