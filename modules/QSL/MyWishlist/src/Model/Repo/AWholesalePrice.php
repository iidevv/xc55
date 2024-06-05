<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Wholesale prices for product
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
abstract class AWholesalePrice extends \CDev\Wholesale\Model\Repo\Base\AWholesalePrice
{
    public function hasWholesalePrice($object)
    {
        return $object->isSnapshotProduct() ? false : parent::hasWholesalePrice($object);
    }
}
