<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Button;

/**
 * Register form in popup
 */
class ForgotPassword extends \XLite\View\Button\PopupButton
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => 'recover_password',
            'widget' => 'XLite\View\RecoverPassword',
        ];
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Forgot password?';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_link.twig';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return 'popup-button forgot';
    }

    /**
     * Default withoutClose value
     *
     * @return boolean
     */
    protected function getDefaultWithoutCloseState()
    {
        return true;
    }
}
