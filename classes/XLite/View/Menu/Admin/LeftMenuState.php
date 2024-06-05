<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * LeftMenuState dynamic widget renders css classes 'expanded or compressed' for left menu
 */
class LeftMenuState extends \XLite\View\AView implements DynamicWidgetInterface
{
    /**
     * Display widget with the default or overriden template.
     *
     * @param string|null $template
     */
    protected function doDisplay($template = null)
    {
        $leftMenuStateCookie = $this->getLeftMenuStateCookie();
        if (isset($leftMenuStateCookie) && $leftMenuStateCookie === 'expanded') {
            echo 'expanded';
        } else {
            echo 'compressed';
        }
    }

    /**
     * Return cookie 'left-menu-state'
     *
     * @return string
     */
    protected function getLeftMenuStateCookie()
    {
        return $_COOKIE['left-menu-state'];
    }

    /**
     * Return widget default template
     *
     * @return string|null
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
