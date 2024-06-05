<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu\Account;

use Qualiteam\SkinActSkin\View\AView;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * @ListChild (list="layout.header.bar.links.account", weight="10000")
 */
class Login extends AView
{
    protected function getDefaultTemplate()
    {
        return 'layout/header/mobile_header_parts/account/login.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && !Auth::getInstance()->isLogged();
    }
}
