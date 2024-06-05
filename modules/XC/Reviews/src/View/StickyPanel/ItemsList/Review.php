<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\StickyPanel\ItemsList;

/**
 * Reviews items list's sticky panel
 */
class Review extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'state'  => [
                'class' => 'XC\Reviews\View\Button\Admin\ReviewStatus',
                'params' => [
                    'label'         => '',
                    'style'         => 'more-action icon-only hide-on-disable hidden',
                    'icon-style'    => 'fa fa-check',
                    'dropDirection' => 'dropup',
                ],
                'position' => 100,
            ],
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 200,
            ],
        ];
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            [],
            'XC\Reviews\View\Button\ItemsExport\Reviews'
        );

        return $list;
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\XC\Reviews\Main';
    }

    protected function getSaveWidgetStyle(): string
    {
        $style = parent::getSaveWidgetStyle();
        if (\XLite::getController()->getTarget() == 'reviews') {
            $style .= ' hide-if-empty-list';
        }
        return $style;
    }
}
