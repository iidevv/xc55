<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\Button;


class WriteMessage extends \XLite\View\Button\PopupButton
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/write_message_popup/style.less';

        return $list;
    }

    /**
     * Return default button label.
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('SkinActOrderMessaging Write a message');
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => 'write_message_popup',
            'widget' => '\Qualiteam\SkinActOrderMessaging\View\WriteMessagePopup',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL()
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' write-message-popup';
    }
}