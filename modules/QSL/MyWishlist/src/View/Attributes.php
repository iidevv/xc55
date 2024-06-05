<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes
 * @Extender\Mixin
 */
abstract class Attributes extends \XLite\View\Product\Details\Customer\Attributes
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getProduct()->isSnapshotProduct() ? false : parent::isVisible();
    }
}
