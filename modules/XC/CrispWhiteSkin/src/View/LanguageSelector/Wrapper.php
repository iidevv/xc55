<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\LanguageSelector;

use XCart\Extender\Mapping\ListChild;

/**
 * Language selector (customer) wrapper
 *
 * @ListChild (list="layout.header.right.settings", weight="200", zone="customer")
 */
class Wrapper extends \XLite\View\LanguageSelector\Customer
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/locale_menu/language.twig';
    }
}
