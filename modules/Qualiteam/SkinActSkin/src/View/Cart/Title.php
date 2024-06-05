<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Cart;

use XLite\Model\Cart;
use XLite\View\AView;

/**
 * Minicart title
 */
class Title extends AView
{
    public function getTitleLabel(): string
    {
        return Cart::getInstance()->isEmpty()
            ? 'Your cart is empty'
            : 'Shopping bag';
    }

    protected function getDefaultTemplate()
    {
        return 'mini_cart/horizontal/parts/title.twig';
    }
}
