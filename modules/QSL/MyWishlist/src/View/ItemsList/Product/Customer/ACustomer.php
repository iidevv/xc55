<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Products items list extension
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * We add the close button controller if needed
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/MyWishlist/items_list/product/close-button.js';

        return $list;
    }
}
