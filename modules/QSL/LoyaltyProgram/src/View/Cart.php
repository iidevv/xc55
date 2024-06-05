<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\Extender;

/**
 * Main widget for the cart page.
 * @Extender\Mixin
 */
class Cart extends \XLite\View\Cart
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/shopping_cart/parts/total.earned_points.css';

        return $list;
    }
}
