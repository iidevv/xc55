<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Quantity widget
 * @Extender\Mixin
 */
abstract class AddButton extends \XLite\View\Product\Details\Customer\AddButton
{
    /**
     * Add from wishlist script emulates the add to cart functionality via JS
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Core\Request::getInstance()->action === 'add_from_wishlist') {
            $list[] = 'modules/QSL/MyWishlist/product/add_from_wishlist.js';
        }

        return $list;
    }
}
