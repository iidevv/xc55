<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Sitemap\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Footer
 *
 * @Extender\Mixin
 * @Extender\Depend ("!CDev\SimpleCMS")
 */
class Footer extends \XLite\View\Menu\Customer\Footer
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['map'] = [
            'label'      => static::t('Sitemap'),
            'url'        => \XLite\Core\Converter::buildURL('map'),
            'controller' => '\XC\Sitemap\Controller\Customer\Map',
        ];

        return $items;
    }
}
