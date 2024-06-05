<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Module\CDev\SimpleCMS\View\FormField\Select\Select2;

use XCart\Extender\Mapping\Extender;
use CDev\ProductAdvisor\Model\Menu;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Pages extends \CDev\SimpleCMS\View\FormField\Select\Select2\Pages
{
    /**
     * @return array
     */
    public static function definePromotionalPages()
    {
        $list = parent::definePromotionalPages();

        $list[Menu::DEFAULT_NEW_ARRIVALS] = static::t('New Arrivals');
        $list[Menu::DEFAULT_COMING_SOON]  = static::t('Coming soon');

        return $list;
    }
}
