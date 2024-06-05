<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        // TODO: Remove after JS-autoloading is added.
        $list[] = 'modules/CDev/Sale/sale_discount_types/js/script.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        // TODO: Remove after CSS-autoloading is added.
        $list[] = 'modules/CDev/Sale/sale_discount_types/css/style.css';

        return $list;
    }
}
