<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\QSL\ShopByBrand\Core\GA\DataMappers;

use XCart\Extender\Mapping\Extender;
use XLite;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ShopByBrand")
 */
class Product extends \CDev\GoogleAnalytics\Core\GA\DataMappers\Product
{
    protected static function getBrand(XLite\Model\Product $product): string
    {
        /** @var \QSL\ShopByBrand\Model\Product $product */
        return $product->getBrandName();
    }
}
