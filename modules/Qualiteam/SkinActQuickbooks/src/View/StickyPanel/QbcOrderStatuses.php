<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\StickyPanel;

use XLite\View\Button\DeleteSelected;
use XLite\View\StickyPanel\ItemsListForm;

class QbcOrderStatuses extends ItemsListForm
{
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => DeleteSelected::class,
                'params'   => [
                    'style' => 'more-action icon-only hide-on-disable hidden',
                ],
                'position' => 100,
            ],
        ];
    }

    protected function defineButtons()
    {
        $list = parent::defineButtons();

        return $list;
    }

    protected function isDisplayORLabel()
    {
        return false;
    }
}