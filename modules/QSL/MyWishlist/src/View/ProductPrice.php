<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\Extender;

/**
 * Wholesale prices for product
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
abstract class ProductPrice extends \CDev\Wholesale\View\ProductPrice
{
    /**
     * Return wholesale prices for the current product
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getWholesalePrices()
    {
        return $this->getProduct()->isSnapshotProduct() ? [] : parent::getWholesalePrices();
    }
}
