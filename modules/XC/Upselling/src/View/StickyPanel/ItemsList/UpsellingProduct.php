<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\View\StickyPanel\ItemsList;

/**
 * U products items list's sticky panel
 */
class UpsellingProduct extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['edit_all'] = [
            'class'    => 'XC\Upselling\View\Button\EditUpsellingProducts',
            'params'   => [
                'style'          => 'more-action always-enabled edit-all',
                'dropDirection'  => 'dropup',
            ],
            'position' => 50,
        ];

        return $list;
    }
}
