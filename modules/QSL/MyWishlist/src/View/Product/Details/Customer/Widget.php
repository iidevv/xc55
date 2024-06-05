<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Quantity widget
 * @Extender\Mixin
 */
abstract class Widget extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if ($this->isWishlistLink()) {
            $this->product = $this->defineSnapshotProduct();
        }

        return parent::getProduct();
    }
}
