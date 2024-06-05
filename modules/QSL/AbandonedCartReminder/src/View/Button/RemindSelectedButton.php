<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Button;

/**
 * Remind Selected button widget for the sticky panel on the Abandoned Carts page.
 */
class RemindSelectedButton extends \XLite\View\Button\PopupButton
{
    /**
     * Register JS files.
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/AbandonedCartReminder/remind_dialog/sticky_button.js';

        return $list;
    }

    /**
     * Return default button label.
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Remind selected customers');
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();
        $list['type'] = 'submit';

        return $list;
    }

    /**
     * Return CSS classes.
     *
     * @return string
     */
    protected function getClass()
    {
        // Remove the popup-button class to prevent the default PopupButton triggers
        return str_replace('popup-button', '', parent::getClass()) . $this->getRemindSelectedButtonClass();
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getRemindSelectedButtonClass()
    {
        return ' submit action remind-selected-button more-action hide-if-empty-list';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return 'regular-main-button';
    }

    /**
     * getDefaultDisableState
     *
     * @return boolean
     */
    protected function getDefaultDisableState()
    {
        return true;
    }

    /**
     * Return URL parameters to use in AJAX popup.
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'    => 'remind_selected_carts',
            'widget'    => '\QSL\AbandonedCartReminder\View\RemindSelectedDialog',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL()
        ];
    }
}
