<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View;

use Qualiteam\SkinActMagicImages\View\Product\Details\Customer\Gallery;
use Qualiteam\SkinActMagicImages\View\Product\Details\Customer\Magic360;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * @inheritDoc
     */
    protected function defineWidgetsCollection()
    {
        $widgets = parent::defineWidgetsCollection();
        $widgets = array_merge(
            $widgets,
            [
                Gallery::class,
                Magic360::class,
            ]
        );
        return array_unique($widgets);
    }
}