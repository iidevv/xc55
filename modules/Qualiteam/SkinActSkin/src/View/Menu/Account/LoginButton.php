<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu\Account;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\View\Authorization;
use XLite\View\Button\PopupButton;

class LoginButton extends PopupButton
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = [
            'target' => 'login',
            'widget' => Authorization::class,
        ];

        if (Request::getInstance()->fromURL) {
            $params['fromURL'] = Request::getInstance()->fromURL;
        }

        return $params;
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Sign in / sign up';
    }

    protected function isVisible()
    {
        return parent::isVisible() && !Auth::getInstance()->isLogged();
    }

    protected function getDefaultWithoutCloseState()
    {
        return true;
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' regular-main-button';
    }
}
