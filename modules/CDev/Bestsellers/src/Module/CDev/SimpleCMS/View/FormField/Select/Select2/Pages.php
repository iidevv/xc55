<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\Module\CDev\SimpleCMS\View\FormField\Select\Select2;

use XCart\Extender\Mapping\Extender;
use CDev\Bestsellers\Model\Menu;

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

        $list[Menu::DEFAULT_BESTSELLERS] = static::t('Bestsellers');

        return $list;
    }
}
