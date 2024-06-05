<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Module\XC\ProductComparison\View;

use XCart\Extender\Mapping\Extender;

/**
 * Add to cart widget
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductComparison")
 */
class AddToCart extends \XC\ProductComparison\View\AddToCart
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !$this->getProduct()->isNotAllowedSubscription() && parent::isVisible();
    }
}
