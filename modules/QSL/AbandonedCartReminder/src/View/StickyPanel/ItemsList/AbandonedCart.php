<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\StickyPanel\ItemsList;

/**
 * Sticky Panel widget for Abandoned Carts page.
 */
class AbandonedCart extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Get the Save button widget.
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            $this->getSaveWidgetParams(),
            $this->getSaveWidgetClass()
        );
    }

    /**
     * Get parameters for the Save button widget.
     *
     * @return array
     */
    protected function getSaveWidgetParams()
    {
        return [];
    }

    /**
     * Get the class name of the Save button widget.
     *
     * @return string
     */
    protected function getSaveWidgetClass()
    {
        return 'QSL\AbandonedCartReminder\View\Button\RemindSelectedButton';
    }

    /**
     * Define additional button widgets.
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list['clearCarts'] = [
            'class' => 'QSL\AbandonedCartReminder\View\Button\ClearSelectedCarts',
            'params' => [
                'disabled'   => true,
                'style'      => 'more-action',
                'icon-style' => 'fa fa-trash-o',
            ],
            'position' => 100,
        ];

        return $list;
    }

    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'QSL-AbandonedCartReminder']);
    }
}
