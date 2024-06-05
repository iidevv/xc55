<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\QSL\ShopByBrand\Core\GA\DataMappers;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ShopByBrand")
 */
class OrderItem extends \CDev\GoogleAnalytics\Core\GA\DataMappers\OrderItem
{
    protected static function getBrand(\XLite\Model\OrderItem $item): string
    {
        return $item->getObject()
            ? $item->getObject()->getBrandName()
            : parent::getBrand($item);
    }
}
