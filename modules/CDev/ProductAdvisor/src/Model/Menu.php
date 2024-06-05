<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Converter;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Menu extends \CDev\SimpleCMS\Model\Menu
{
    use ExecuteCachedTrait;

    public const DEFAULT_NEW_ARRIVALS = '{new arrivals}';
    public const DEFAULT_COMING_SOON  = '{coming soon}';

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        $list = parent::defineLinkURLs();

        $list += $this->executeCachedRuntime(static function () {
            return [
                static::DEFAULT_NEW_ARRIVALS => Converter::buildURL('new_arrivals'),
                static::DEFAULT_COMING_SOON  => Converter::buildURL('coming_soon'),
            ];
        }, 'product_advisor');

        return $list;
    }

    /**
     * Defines the link controller class names for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkControllers()
    {
        $list = parent::defineLinkControllers();

        $list[static::DEFAULT_COMING_SOON] = '\CDev\ProductAdvisor\Controller\Customer\ComingSoon';
        $list[static::DEFAULT_NEW_ARRIVALS] = '\CDev\ProductAdvisor\Controller\Customer\NewArrivals';

        return $list;
    }
}
