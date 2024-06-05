<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\StickyPanel\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AAdmin extends \XLite\View\StickyPanel\Product\Admin\AAdmin
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Sale/sticky_panel/product_list/script.js'
        ]);
    }
}
