<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\Extender;

/**
 *
 * @Extender\Mixin
 */
abstract class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * Get the view classes collection
     *
     * @return array
     */
    public function getWidgetsCollection()
    {
        return $this->getProduct()->isSnapshotProduct() ? [] : parent::getWidgetsCollection();
    }
}
