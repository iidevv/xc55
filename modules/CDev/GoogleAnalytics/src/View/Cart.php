<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 *
 * Abstract widget
 */
abstract class Cart extends \XLite\View\Cart
{
    /**
     * Register JS files
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/GoogleAnalytics/cart/parts/cart_view.js';

        return $list;
    }
}
