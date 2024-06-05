<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\View\ItemsList\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AProduct extends \XLite\View\ItemsList\Product\AProduct
{
    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_BOUGHT = 'p.sales';
}
