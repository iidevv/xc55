<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XC\ThemeTweaker\View\ThemeTweakerPanel;
use XLite;
use XLite\Controller\Admin\AAdmin;
use XLite\Core\Request;

/**
 * ThemeTweaker controller
 */
class ThemeTweakerSwitcher extends AAdmin
{
    /**
     * Get redirect URL for the controller.
     *
     * @param bool $wasThemeTweakerEnabled
     *
     * @return string
     */
    protected function getRedirectLink(bool $wasThemeTweakerEnabled)
    {
        $url = (string)Request::getInstance()->returnURL;
        if (!$url) {
            $url = $wasThemeTweakerEnabled
                ? XLite\Core\Converter::buildURL('', '', [], XLite::getCustomerScript())
                : XLite\Core\Converter::buildURL('layout');
        }
        return $url;
    }

    protected function run()
    {
        $shouldEnable = ((string)Request::getInstance()->switch === 'on');
        ThemeTweakerPanel::switchThemeTweaker($shouldEnable);
        $this->redirect(
            $this->getRedirectLink($shouldEnable),
            302
        );
    }
}
