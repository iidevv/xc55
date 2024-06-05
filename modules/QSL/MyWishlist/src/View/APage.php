<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\Extender;

/**
 * APage
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        return $this->getProduct()->isSnapshotProduct() ? false : parent::isLoupeVisible();
    }
}
