<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\StickyPanel\EducationalVideo\Admin;

class StickyPanel extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'status' => [
                'class'    => 'XLite\View\Button\Dropdown\Status',
                'params'   => [
                    'label'         => '',
                    'style'         => 'always-enabled more-action icon-only hide-on-disable',
                    'icon-style'    => 'fa fa-power-off iconfont',
                    'dropDirection' => 'dropup',
                ],
                'position' => 200,
            ],
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 400,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/Qualiteam/SkinActVideoFeature/sticky_panel/educational_videos/script.js'
        ]);
    }
}