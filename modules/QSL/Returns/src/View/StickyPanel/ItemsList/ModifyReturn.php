<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\StickyPanel\ItemsList;

/**
 * Modify return items list's sticky panel
 */
class ModifyReturn extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Check panel has more actions buttons
     *
     * @return boolean
     */
    protected function hasMoreActionsButtons()
    {
        return true;
    }

    /**
     * Get "save" widget
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSaveWidget()
    {
        $widget = $this->getWidget(
            [
                'style'    => 'action submit',
                'label'    => $this->getSaveWidgetLabel(),
                'disabled' => true,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getSaveWidgetStyle(),
            ],
            'QSL\Returns\View\Button\Admin\ModifyReturn\Modify'
        );

        return $widget;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list['complete'] = $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_STYLE    => 'always-enabled',
                \XLite\View\Button\AButton::PARAM_LABEL    => static::t('Authorize'),
                \XLite\View\Button\AButton::PARAM_DISABLED => false,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getCompleteWidgetStyle(),
            ],
            'QSL\Returns\View\Button\Admin\ModifyReturn\Authorize'
        );

        $list['decline'] = $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_STYLE    => 'always-enabled',
                \XLite\View\Button\AButton::PARAM_LABEL    => static::t('Decline'),
                \XLite\View\Button\AButton::PARAM_DISABLED => false,
            ],
            'QSL\Returns\View\Button\Admin\ModifyReturn\Decline'
        );

        $list['delete'] = $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_STYLE    => 'always-enabled',
                \XLite\View\Button\AButton::PARAM_LABEL    => static::t('Delete'),
                \XLite\View\Button\AButton::PARAM_DISABLED => false,
            ],
            'QSL\Returns\View\Button\Admin\ModifyReturn\Delete'
        );

        return $list;
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Modify');
    }

    /**
     * Defines the style for the save button
     *
     * @return string
     */
    protected function getSaveWidgetStyle()
    {
        return 'regular-button';
    }


    /**
     * Defines the style for the complete button
     *
     * @return string
     */
    protected function getCompleteWidgetStyle()
    {
        return 'regular-main-button';
    }
}
