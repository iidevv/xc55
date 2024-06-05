<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Checkout;

use XCart\Extender\Mapping\Extender;
use XC\FastLaneCheckout\Main;

/**
 * Cart items
 * @Extender\Mixin
 */
class CartItems extends \XLite\View\Checkout\CartItems
{
    protected function getItemsCountLinkAttributes()
    {
        $attrs = parent::getItemsCountLinkAttributes();

        if (Main::isFastlaneEnabled()) {
            $attrs['@click.prevent'] = 'toggleItems';
        }

        return $attrs;
    }
}
