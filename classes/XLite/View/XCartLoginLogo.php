<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * X-Cart Logo
 *
 * @ListChild (list="admin.main.page.header", weight="100", zone="admin")
 */
class XCartLoginLogo extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'header/parts/logo_login_page.twig';
    }

    protected function getLogoPath()
    {
        return 'images/logo.svg';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !\XLite\Core\Auth::getInstance()->isLogged() || !\XLite\Core\Auth::getInstance()->isAdmin();
    }
}
