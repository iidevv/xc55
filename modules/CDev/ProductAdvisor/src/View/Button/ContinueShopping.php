<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ContinueShopping extends \XLite\View\Button\ContinueShopping
{
    /**
     * Returns allowed continue shopping targets
     *
     * @return array
     */
    protected function getAllowedContinueShoppingTargets()
    {
        return array_merge(
            parent::getAllowedContinueShoppingTargets(),
            ['sale_products', 'new_arrivals', 'coming_soon']
        );
    }
}
