<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ContactUs\Module\CDev\SimpleCMS\View\FormField\Select\Select2;

use XCart\Extender\Mapping\Extender;
use CDev\ContactUs\Model\Menu;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Pages extends \CDev\SimpleCMS\View\FormField\Select\Select2\Pages
{
    /**
     * @return array
     */
    public static function getAllPages()
    {
        $list = parent::getAllPages();

        $list[Menu::DEFAULT_CONTACT_US_PAGE] = static::t('Contact us');

        return $list;
    }
}
