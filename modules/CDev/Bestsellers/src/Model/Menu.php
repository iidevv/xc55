<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\Model;

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

    public const DEFAULT_BESTSELLERS = '{bestsellers}';

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        $list = parent::defineLinkURLs();
        $list[static::DEFAULT_BESTSELLERS] = $this->executeCachedRuntime(static function () {
            return Converter::buildURL('bestsellers');
        }, ['bestsellers']);

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
        $list[static::DEFAULT_BESTSELLERS] = 'CDev\Bestsellers\Controller\Customer\Bestsellers';

        return $list;
    }
}
