<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\StickyPanel;

use XLite\View\Button\AButton;
use Qualiteam\SkinActQuickbooks\View\Button\ResetSelected;
use Qualiteam\SkinActQuickbooks\View\Button\ResetAll;
use XLite\View\StickyPanel\ItemsListForm;

class QuickbooksSyncErrors extends ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'reset_all' => [
                'class'    => ResetAll::class,
                'params'   => [
                    'style' => 'action always-enabled',
                ],
                'position' => 100,
            ],
        ];
    }
    
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        
        $list['save'] = $this->getWidget(
            [
                'style'    => 'more-action',
                'label'    => static::t('Quickbooks reset selected errors'),
                'disabled' => true,
                AButton::PARAM_BTN_TYPE => 'regular-button',
                'position' => 10,
            ],
            ResetSelected::class
        );

        return $list;
    }

    protected function isDisplayORLabel()
    {
        return true;
    }
}