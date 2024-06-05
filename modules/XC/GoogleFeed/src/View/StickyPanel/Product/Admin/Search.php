<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\StickyPanel\Product\Admin;

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
        $list['google_feed'] = [
            'class'    => 'XC\GoogleFeed\View\Button\Dropdown\GoogleSwitcher',
            'params'   => [
                'label'         => '',
                'style'         => 'more-action icon-only hide-on-disable hidden',
                'icon-style'    => 'fa fa-google',
                'useCaretButton' => false,
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}
