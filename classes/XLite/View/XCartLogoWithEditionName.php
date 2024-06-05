<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.main.page.menu.header", weight="100", zone="admin")
 */
class XCartLogoWithEditionName extends \XLite\View\AView
{
    protected function getDefaultTemplate(): string
    {
        return 'left_menu/logo_with_edition_name.twig';
    }

    protected function getLogoPath(): string
    {
        return 'images/logo.svg';
    }
}
