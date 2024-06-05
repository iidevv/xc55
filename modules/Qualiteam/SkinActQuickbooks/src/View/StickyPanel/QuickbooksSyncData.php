<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\StickyPanel;

use Qualiteam\SkinActQuickbooks\View\Button\UnlinkSelected;
use XLite\View\StickyPanel\ItemsListForm;

class QuickbooksSyncData extends ItemsListForm
{
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => UnlinkSelected::class,
                'params'   => [
                    'style' => 'more-action icon-only disabled',
                ],
                'position' => 100,
            ],
        ];
    }

    protected function defineButtons()
    {
        $list = parent::defineButtons();

        unset($list['save']);

        return $list;
    }

    protected function isDisplayORLabel()
    {
        return false;
    }
}