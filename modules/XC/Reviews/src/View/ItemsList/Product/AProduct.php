<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\ItemsList\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list
 * @Extender\Mixin
 */
abstract class AProduct extends \XLite\View\ItemsList\Product\AProduct
{
    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_RATE = 'r.rating';
}
