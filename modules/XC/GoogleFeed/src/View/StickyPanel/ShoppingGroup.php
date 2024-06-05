<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\StickyPanel;

/**
 * Google shopping groups sticky panel
 */
class ShoppingGroup extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'shopping-group'  => [
                'class'    => 'XC\GoogleFeed\View\Button\Dropdown\ShoppingGroup',
                'params'   => [
                    'label'         => 'Assign shopping group',
                    'style'         => 'more-action hide-on-disable hidden',
                    'dropDirection' => 'dropup',
                ],
                'position' => 100,
            ],
        ];
    }
}
