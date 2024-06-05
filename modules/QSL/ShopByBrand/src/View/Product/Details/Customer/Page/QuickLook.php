<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Quick Look widget.
 * @Extender\Mixin
 */
class QuickLook extends \XLite\View\Product\Details\Customer\Page\QuickLook
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/ShopByBrand/brand/brand.css';

        return $list;
    }
}
