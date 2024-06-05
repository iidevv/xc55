<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\StickyPanel\ItemsList;

/**
 * Tag items list's sticky panel
 */
class Tag extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
        ];
    }

    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'XC-ProductTags']);
    }

    protected function getSaveWidgetStyle(): string
    {
        return parent::getSaveWidgetStyle() . ' hide-if-empty-list';
    }
}
