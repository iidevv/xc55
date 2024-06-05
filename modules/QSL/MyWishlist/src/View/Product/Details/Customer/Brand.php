<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Widget promoting reward points which customers will earn after purchasing the product.
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\ShopByBrand")
 */
abstract class Brand extends \QSL\ShopByBrand\View\Product\Details\Brand
{
    /**
     * Check if the product has an associated brand.
     *
     * @return boolean
     */
    public function hasBrand()
    {
        return $this->isWishlistLink() ? false : parent::hasBrand();
    }
}
