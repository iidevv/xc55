<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\StickyPanel;

use XLite\View\Button\DeleteSelected;
use XLite\View\StickyPanel\ItemsListForm;

class Logs extends ItemsListForm
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
        if (isset($list['save'])) {
            unset($list['save']);
        }

        return $list;
    }

    protected function isDisplayORLabel()
    {
        return false;
    }
}
