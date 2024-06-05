<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\StickyPanel\Profile\Admin;

/**
 * Profiles items list's sticky panel
 */
class Profile extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/stickyPanelUserSelection.js';

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();
        $class .= ' user-selection-sticky-panel';

        return $class;
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Select user');
    }


    protected function getSaveWidget()
    {
        $widget = $this->getWidget(
            [
                'style'    => 'action submit',
                'label'    => $this->getSaveWidgetLabel(),
                'disabled' => true,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getSaveWidgetStyle(),
                'jsCode' => 'processSelectUserClick();'
            ],
            \XLite\View\Button\Regular::class
        );

        return $widget;
    }

    /**
     * Defines the style for the save button
     *
     * @return string
     */
    protected function getSaveWidgetStyle()
    {
        return parent::getSaveWidgetStyle() . ' select-user-btn';
    }

}
